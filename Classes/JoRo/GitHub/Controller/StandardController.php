<?php
namespace JoRo\GitHub\Controller;

/*
 * This file is part of the JoRo.GitHub package.
 */

use Neos\Flow\Annotations as Flow;
use Milo\Github;
use Neos\Flow\Error\Debugger;

class StandardController extends \Neos\Flow\Mvc\Controller\ActionController
{

    /**
     * @return void
     */
    public function indexAction()
    {

        $id = $this->request->getInternalArgument('__id');

        $username = $this->request->getInternalArgument('__username');
        $token = $this->request->getInternalArgument('__token');
        $activityCount = $this->request->getInternalArgument('__activityCount');
        $repoCount = $this->request->getInternalArgument('__repoCount');
        $small = $this->request->getInternalArgument('__small');
        $medium = $this->request->getInternalArgument('__medium');
        $large = $this->request->getInternalArgument('__large');

        $api = new Github\Api;

        $token = new \Milo\Github\OAuth\Token($token);

        $api->setToken($token);
        $api->getToken();
        $user = $username;

        /*
            GET OWN REPOS ONLY
        */
        $response = $api->get('/users/:user/repos', [
            'user' => $user,
        ]);
        $repos = $api->decode($response);

        $i = 0;

        foreach ($repos as $repo) {
            if ($repo->fork != null) {
                unset($repos[$i]);
            }
            $i++;
            $responseLang = $api->get('/repos/:owner/:repo/languages', [
                'owner' => $user,
                'repo' => $repo->name,
            ]);
            $repoLang = $api->decode($responseLang);
            $total = 0;
            foreach($repoLang as $lang) {
                $total = $total + $lang;
            }

            // CALC PERCENTAGE
            $languages = [];
            foreach($repoLang as $key => $value) {
                $percent = ($value * 100) / $total;
                array_push($languages, ['name' => $key, 'percent' => round($percent, 2)]);
            }
            $repo->languages = $languages;
        }

        $repoList = [];
        foreach ($repos as $repo) {
            array_push($repoList, ['name' => $repo->name, 'description' => $repo->description, 'url' => $repo->html_url]);
        }
        $this->view->assign('repoList', $repos);

        /*
            GET USER DETAILS
        */
        $response = $api->get('/users/:user/events', [
            'user' => $user,
        ]);
        $userEvents = $api->decode($response);
        $this->view->assign('activities', $userEvents);

        /*
            GET USER DETAILS
        */
        $response = $api->get('/users/:user/', [
            'user' => $user,
        ]);
        $userDetails = $api->decode($response);
        $this->view->assign("details", $userDetails);

        $this->view->assign("id", $id);
        $this->view->assign("activityCount", $activityCount - 1);
        $this->view->assign("repoCount", $repoCount - 1);
        $this->view->assignMultiple([
            'small' => $small,
            'medium' => $medium,
            'large' => $large,
        ]);
    }
}

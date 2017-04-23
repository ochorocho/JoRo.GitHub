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
     * @Flow\InjectConfiguration(path="credentials.username")
     * @var string
     */
    protected $username;

    /**
     * @Flow\InjectConfiguration(path="credentials.token")
     * @var string
     */
    protected $token;

    /**
     * @return void
     */
    public function indexAction()
    {

        $arguments = $this->request->getInternalArgument('__node')->getNodeData()->getProperties();
        $id = $this->request->getInternalArgument('__node')->getNodeData()->getIdentifier();

        $user = $arguments['username'];
        $tokenAuth = $arguments['token'];
        $activityCount = $arguments['activityCount'];
        $repoCount = $arguments['repoCount'];
        $small = $arguments['small'];
        $medium = $arguments['medium'];
        $large = $arguments['large'];
        $layout = strtolower($arguments['layout']);


        if(empty($user) || empty($tokenAuth)) {
            $access = false;
        } else {
            $access = true;
            $api = new Github\Api;
            $tokenAuth = new \Milo\Github\OAuth\Token($tokenAuth);

            $api->setToken($tokenAuth);
            $api->getToken();

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
            // CALC MEGABYTES TO BYTES ...
            $userDetails->disk_usage = $userDetails->disk_usage * 1024 * 1024;

            $this->view->assign("details", $userDetails);
        }

        $this->view->assign("id", $id);
        $this->view->assign("activityCount", $activityCount - 1);
        $this->view->assign("repoCount", $repoCount - 1);
        $this->view->assignMultiple([
            'small' => $small - 1,
            'medium' => $medium - 1,
            'large' => $large - 1,
            'access' => $access,
            'layout' => $layout
        ]);
    }
    
    /**
     * @return void
     */
    public function gistAction() {

        $arguments = $this->request->getInternalArgument('__node')->getNodeData()->getProperties();
        $id = $this->request->getInternalArgument('__node')->getNodeData()->getIdentifier();

        $api = new Github\Api;
        $tokenAuth = new \Milo\Github\OAuth\Token($this->token);

        $api->setToken($tokenAuth);
        $api->getToken();

        $gistId = $arguments['gist'];
        $gistUrl = "https://gist.github.com/" . $this->username ."/" . $gistId . ".js";

        /*
            GET SINGLE GIST
        */
        $response = $api->get('/gists/:id', [
            'id' => $gistId,
        ]);
        $gist = $api->decode($response);

        $this->view->assign("gist", $gist);
        $this->view->assign("id", $id);
        $this->view->assign("gistUrl", $gistUrl);
    }
}

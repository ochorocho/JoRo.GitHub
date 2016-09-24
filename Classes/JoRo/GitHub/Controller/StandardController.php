<?php
namespace JoRo\GitHub\Controller;

/*
 * This file is part of the JoRo.GitHub package.
 */

use TYPO3\Flow\Annotations as Flow;
use Milo\Github;
use TYPO3\Flow\Error\Debugger;

class StandardController extends \TYPO3\Flow\Mvc\Controller\ActionController
{

    /**
     * @return void
     */
    public function indexAction()
    {

        $myNodeName = $this->request->getInternalArgument('__myNodeName');

        $api = new Github\Api;

        $token = new \Milo\Github\OAuth\Token('f2ef0f5c44b674298f97a6ce88bd2202c350b22f');

        $api->setToken($token);
        $api->getToken();
        $user = 'ochorocho';

        /*
            GET OWN REPOS ONLY
        */
        $response = $api->get('/users/:user/repos', [
            'user' => $user,
        ]);
        $repos = $api->decode($response);

        //\TYPO3\Flow\var_dump($repos);

        $i = 0;

        foreach ($repos as $repo) {
            if ($repo->fork != null) {
                unset($repos[$i]);
            }
            $i++;
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

        //\TYPO3\Flow\var_dump($userEvents);

        $activity = [];
        /*
        foreach ($userEvents as $event) {
            switch ($event->type) {
                case 'ForkEvent':
                    $name = $event->payload->forkee->full_name;
                    break;
                case 'PullRequestEvent':
                    $name = $event->payload->pull_request->title;
                    break;
                case 'PushEvent':
                    $name = 'Pushed to ' . $event->payload->ref;
                    break;
                case 'CreateEvent':
                    $name = $event->payload->ref;
                    break;
                case 'IssuesEvent':
                case 'IssueCommentEvent':
                    $name = $event->repo->name . '#' . $event->payload->issue->number . ' ' . $event->payload->issue->title;
                    break;
            }
            array_push($activity, ['name' => $name, 'type' => $event->type]);
        }
        */
        $this->view->assign('activities', $userEvents);

        /*
            GET USER DETAILS
        */
        $response = $api->get('/users/:user/', [
            'user' => $user,
        ]);
        $userDetails = $api->decode($response);
        //\TYPO3\Flow\var_dump($userDetails);
        $this->view->assign("details", $userDetails);
    }
}

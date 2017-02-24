<?php
namespace JoRo\GitHub\NodeTypePostprocessor;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "My.Package".                   *
 *                                                                               *
 *                                                                               */

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeType;
use Neos\ContentRepository\NodeTypePostprocessor\NodeTypePostprocessorInterface;
use Milo\Github;


/**
 * Class GistPostprocessor
 *
 * @package JoRo\GitHub\GistPostprocessor
 */
class GistPostprocessor implements NodeTypePostprocessorInterface {

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
	 * Returns the processed Configuration
	 *
	 * @param NodeType $nodeType (uninitialized) The node type to process
	 * @param array $configuration input configuration
	 * @param array $options The processor options
	 * @return void
	 */
	public function process(NodeType $nodeType, array &$configuration, array $options) {

		$api = new Github\Api;
		$token = new \Milo\Github\OAuth\Token($this->token);

		$api->setToken($token);
		$api->getToken();

		$user = $this->username;

		/*
            GET AVAILABLE GISTS
        */
		$response = $api->get('/users/:user/gists', [
			'user' => $user,
		]);
		$gists = $api->decode($response);

		$options = [];
		$options['empty'] = ['label' => ''];
		foreach($gists as $gist) {
			if(empty($gist->description)) {
				$options[$gist->id] = ['label' => 'ID ' . $gist->id];
			} else {
				$options[$gist->id] = ['label' => $gist->description];
			}
		}

		$propertyName = 'gist';
		$configuration['properties'][$propertyName] = [
			'type' => 'string',
			'ui' => [
				'label' => 'Select Gist to show',
				'reloadIfChanged' => true,
				'inspector' => [
					'group' => 'gist',
					'position' => 500,
					'editor' => 'Neos.Neos/Inspector/Editors/SelectBoxEditor',
					'editorOptions' => [
						'values' => $options
					],
				],
			],
		];
	}
}
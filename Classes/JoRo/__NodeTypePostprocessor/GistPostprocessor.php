<?php
namespace JoRo\GitHub\NodeTypePostprocessor;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "My.Package".                   *
 *                                                                               *
 *                                                                               */

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeType;
use Neos\ContentRepository\NodeTypePostprocessor\NodeTypePostprocessorInterface;
use Neos\Media\Domain\Repository\TagRepository;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Media\Domain\Model\Tag;

/**
 * Class TagsPostprocessor
 *
 * @package JoRo\GitHub\GistPostprocessor
 */
class GistPostprocessor implements NodeTypePostprocessorInterface {
	/**
	 * @var GistRepository
	 * @Flow\Inject
	 */
	protected $gistRepository;
	/**
	 * @var PersistenceManagerInterface
	 * @Flow\Inject
	 */
	protected $persistenceManager;
	/**
	 * Returns the processed Configuration
	 *
	 * @param NodeType $nodeType (uninitialized) The node type to process
	 * @param array $configuration input configuration
	 * @param array $options The processor options
	 * @return void
	 */
	public function process(NodeType $nodeType, array &$configuration, array $options) {
		$tags = $this->tagRepository->findAll();
		if ($tags->count() === 0) {
			return;
		}

		$options = array();
		$tags->rewind();
		$i = 0;
		while ($tags->valid()) {
			/** @var Tag $currentTag */
			$i++;
			$currentTag = $tags->current();
			$options[$currentTag->getLabel()] = array('label' => $currentTag->getLabel());
			$tags->next();
		}

		$propertyName = 'tag';
		$configuration['properties'][$propertyName] = [
			'type' => 'string',
			'ui' => [
				'label' => 'Show Images by Tag',
				'reloadIfChanged' => true,
				'inspector' => [
					'group' => 'TagGroup',
					'position' => 500,
					'editor' => 'Neos.Neos/Inspector/Editors/SelectBoxEditor',
					'editorOptions' => [
						'values' => $options,
					],
				],
			],
		];
						
	}
}
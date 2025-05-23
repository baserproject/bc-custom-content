<?php
/**
 * CuCustomField : baserCMS Custom Field Text Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @license          MIT LICENSE
 */

namespace BcCcRelated\View\Helper;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\View\Helper\BcAdminFormHelper;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * Class BcCcRelatedHelper
 *
 * @property BcAdminFormHelper $BcAdminForm
 */
#[\AllowDynamicProperties]
class BcCcRelatedHelper extends Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Helper
     * @var string[]
     */
    public array $helpers = [
        'BaserCore.BcAdminForm' => ['templates' => 'BaserCore.bc_form']
    ];

    /**
     * テーブルリストを取得する
     * @return array
     */
    public function getTableList()
    {
        /** @var CustomTablesServiceInterface $tablesService */
        $tablesService = $this->getService(CustomTablesServiceInterface::class);
        return $tablesService->getList(['type' => 2]);
    }

    /**
     * control
     *
     * @param string $fieldName
     * @param CustomField $field
     * @param array $options
     * @return string
     */
    public function control(CustomLink $link, array $options = []): string
    {
        $field = $link->custom_field;
        if(empty($field->meta['BcCcRelated']['custom_table_id'])) return '';
        $filterName = $field->meta['BcCcRelated']['filter_name'];
        $filterValue = $field->meta['BcCcRelated']['filter_value'];
        $conditions = [];
        if($filterName && $filterValue) {
            $conditions['conditions'] = ['CustomEntries.' . $filterName => $filterValue];
        }

        /** @var CustomEntriesServiceInterface $entriesService */
        $entriesService = $this->getService(CustomEntriesServiceInterface::class);
        $currentTableId = $entriesService->CustomEntries->tableId;
        try {
            $entriesService->setup($field->meta['BcCcRelated']['custom_table_id']);
            $list = $entriesService->getList($conditions);
        } catch (\Throwable $e) {
            $list = [];
        }

        $options = array_merge([
            'type' => 'select',
            'options' => $list,
            'empty' => __d('baser_core', '選択してください'),
        ], $options);

        // プレビューの場合はテーブルIDが存在しない
        if($currentTableId) {
            $entriesService->setup($currentTableId);
        }
        return $this->BcAdminForm->control($link->name, $options);
    }

    /**
     * プレビュー
     *
     * @param CustomLink $link
     * @return string
     */
    public function preview(CustomLink $link)
    {
        $options = [
            ':value' => 'entity.default_value'
        ];
        return $this->control($link, $options) . '<br>※ 関連データはリアルタイムでのプレビューは未対応です。保存してから確認してください。';
    }

    /**
     * Search Control
     * @param string $fieldName
     * @param CustomField $field
     * @param array $options
     * @return string
     */
    public function searchControl(CustomLink $link, array $options = []): string
    {
        return $this->control($link, $options);
    }

    /**
     * Get
     *
     * @param mixed $fieldValue
     * @param CustomLink $link
     * @param array $options
     * @return mixed
     */
    public function get($fieldValue, CustomLink $link, array $options = [])
    {
        $options = array_merge([
            'getRelatedBody' => false
        ], $options);

        if (!$fieldValue) return '';

        /** @var CustomEntriesServiceInterface $entriesService */
        $entriesService = $this->getService(CustomEntriesServiceInterface::class);
        $entriesService->setup($link->custom_field->meta['BcCcRelated']['custom_table_id']);
        $entry = $entriesService->get($fieldValue, ['contain' => 'CustomTables']);

        if ($options['getRelatedBody'])
            return $entry;

        return $entry->{$entry->custom_table->display_field};
    }

    public function getFieldItemList(Int $contentId, string $fieldName)
    {
        $customFieldsTable = TableRegistry::getTableLocator()->get('BcCustomContent.CustomFields');
        $customField = $customFieldsTable->find()
            ->contain([
                'CustomLinks' => function($q) use($fieldName) {
                    return $q->where(['CustomLinks.name' => $fieldName]);
                },
                'CustomLinks.CustomTables',
                'CustomLinks.CustomTables.CustomContents',
                'CustomLinks.CustomTables.CustomContents.Contents' => function($q) use($contentId) {
                    return $q->where( ['Contents.id' => (int)$contentId]);
                },
            ])->first();

        $displayField = $customField->custom_links[0]->custom_table->display_field;
        $customTableId = $customField->meta['BcCcRelated']['custom_table_id']?? null;
        if($customTableId === null) return [];

        $customEntriesTable = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries');
        $customEntriesTable->setup($customTableId);
        $query = $customEntriesTable->find()
            ->select([
                'CustomEntries.id',
                'CustomEntries.' . $displayField
        ]);

        $filterName = $customField->meta['BcCcRelated']['filter_name']?? null;
        $filterValue = $customField->meta['BcCcRelated']['filter_value']?? null;

        if($filterName && $filterValue) {
            $query->where(['CustomEntries.' . $filterName => $filterValue]);
        }
        $customEntries = $query->all()->toArray();

        $entryTitles = Hash::combine($customEntries, '{n}.id', '{n}.' . $displayField);

        return $entryTitles;
    }

}

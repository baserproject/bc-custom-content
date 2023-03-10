<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcCustomContent\Service;

use BaserCore\Model\Entity\Content;
use BaserCore\Service\BcDatabaseService;
use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Service\UsersService;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcCustomContent\Model\Entity\CustomEntry;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\Model\Entity\CustomTable;
use BcCustomContent\Model\Table\CustomEntriesTable;
use BcCustomContent\Model\Table\CustomTablesTable;
use BcCustomContent\Utility\CustomContentUtil;
use BcCustomContent\View\Helper\CustomContentArrayTrait;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Utility\Hash;

/**
 * CustomEntriesService
 *
 * @property CustomEntriesTable $CustomEntries
 * @property CustomTablesTable $CustomTables
 * @property BcDatabaseService $BcDatabaseService
 */
class CustomEntriesService implements CustomEntriesServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use CustomContentArrayTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->CustomEntries = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries');
        $this->CustomTables = TableRegistry::getTableLocator()->get('BcCustomContent.CustomTables');
        $this->BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
    }

    /**
     * ?????????????????????????????????????????????????????????????????????
     *
     * @param int $tableId
     * @return EntityInterface
     */
    public function getNew(int $tableId)
    {
        $default = [
            'custom_table_id' => $tableId,
            'creator_id' => BcUtil::loginUser()->id,
            'published' => FrozenTime::now(),
            'status' => 0
        ];

        if ($this->CustomEntries->links) {
            foreach($this->CustomEntries->links as $link) {
                /** @var CustomLink $link */
                if ($link->custom_field->default_value) {
                    if (CustomContentUtil::getPluginSetting($link->custom_field->type, 'controlType') === 'multiCheckbox') {
                        $default[$link->name] = $this->textToArray($link->custom_field->default_value);
                    } else {
                        $default[$link->name] = $link->custom_field->default_value;
                    }
                }
            }
        }

        // newEntity() ??????????????????????????????????????? null ????????????????????????????????????????????????????????????????????????
        return new CustomEntry($default, ['source' => 'BcCustomContent.CustomEntries']);
    }

    public function getFieldControlType(string $type)
    {
        return Configure::read("BcCustomContent.fieldTypes.$type.controlType");
    }

    /**
     * ?????????????????????????????????????????????????????????????????????
     *
     * ?????????????????????????????????????????????????????????
     *
     * @param int $tableId
     * @checked
     * @noTodo
     */
    public function setup(int $tableId, array $postData = [])
    {
        $this->CustomEntries->setup($tableId, $postData);
    }

    /**
     * ???????????????????????????????????????????????????
     *
     * @return \Cake\ORM\Query
     */
    public function getIndex(array $queryParams = [])
    {
        $options = array_merge([
            'limit' => null,
            'direction' => '',    // ????????????
            'order' => '',    // ?????????????????????????????????
            'contain' => ['CustomTables'],
            'status' => '',
            'use_api' => null
        ], $queryParams);

        $query = $this->CustomEntries->find()
            ->select($this->createSelect($options))
            ->select($this->CustomEntries->CustomTables)
            ->contain($options['contain']);

        if ($options['order']) {
            $query->order($this->createOrder($options['order'], $options['direction']));
        }

        if (!empty($options['limit'])) {
            $query->limit($options['limit']);
        }

        unset($options['order'], $options['direction'], $options['limit']);

        if (!empty($options)) {
            $query = $this->createIndexConditions($query, $options);
        }

        return $query;
    }

    /**
     * getTreeIndex
     *
     * @param int $blogContentId
     * @param array $queryParams
     * @return \ArrayObject
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTreeIndex(array $queryParams): \ArrayObject
    {
        $srcEntities = $this->CustomEntries->find('treeList')->order(['lft'])->all();
        $entities = [];
        foreach($srcEntities->toArray() as $key => $value) {
            /* @var CustomEntry $entity */
            $entity = $this->CustomEntries->find()->where(['id' => $key])->first();
            if (!preg_match("/^([_]+)/i", $value, $matches)) {
                $entity->depth = 0;
                $entities[] = $entity;
                continue;
            }
            $entity->title = sprintf(
                "%s???%s",
                str_replace('_', '&nbsp;&nbsp;&nbsp;&nbsp;', $matches[1]),
                $entity->title
            );
            $entity->depth = strlen($matches[1]);
            $entities[] = $entity;
        }
        return new \ArrayObject($entities);
    }

    /**
     * ???????????????????????????????????????
     *
     * @param Query $query
     * @param array $params
     * @return Query
     */
    public function createIndexConditions(Query $query, array $params)
    {
        foreach($params as $key => $value) {
            if ($value === '') unset($params[$key]);
        }
        if (empty($params)) return $query;

        $params = array_merge([
            'title' => null,
            'creator_id' => null,
            'status' => null,
        ], $params);

        // ????????????
        if ($params['status'] === 'publish') {
            $conditions = $this->CustomEntries->getConditionAllowPublish();
        } elseif ($params['status'] === 'unpublish') {
            $conditions = ['CustomEntries.status' => false];
        } else {
            $conditions = [];
        }

        // ???????????????????????????
        if (!is_null($params['title'])) {
            $conditions['or'] = [
                'CustomEntries.title LIKE' => '%' . $params['title'] . '%',
                'CustomEntries.name LIKE' => '%' . $params['title'] . '%'
            ];
        }

        // ?????????
        if (!is_null($params['creator_id'])) {
            $conditions['CustomEntries.creator_id'] = $params['creator_id'];
        }

        unset($params['status'], $params['title'], $params['creator_id']);
        if (!$params) return $query->where($conditions);

        /** @var CustomLinksService $linksService */
        $linksService = $this->getService(CustomLinksServiceInterface::class);
        $links = $linksService->getIndex($this->CustomEntries->tableId, ['finder' => 'all'])->all()->toArray();
        $linksArray = array_combine(Hash::extract($links, '{n}.name'), array_values($links));
        if ($linksArray) {
            foreach($params as $key => $value) {
                if (!isset($linksArray[$key])) continue;

                /** @var CustomLink $link */
                $link = $linksArray[$key];

                if (BcUtil::isAdminSystem()) {
                    if (!$link->search_target_admin) continue;
                } else {
                    if (!$link->search_target_front) continue;
                }

                $controlType = CustomContentUtil::getPluginSetting($link->custom_field->type, 'controlType');
                if (in_array($controlType, ['text', 'textarea'])) {
                    $conditions["CustomEntries.$key LIKE"] = '%' . $value . '%';
                } elseif ($controlType === 'multiCheckbox' && is_array($value)) {
                    $c = [];
                    foreach($value as $v) {
                        $c[] = ["CustomEntries.$key LIKE" => '%"' . $v . '"%'];
                    }
                    $conditions[] = ['AND' => $c];
                } elseif ($controlType === 'checkbox') {
                    if ($value) $conditions["CustomEntries.$key"] = $value;
                } else {
                    $conditions["CustomEntries.$key"] = $value;
                }
            }
        }

        return $query->where($conditions);
    }

    /**
     * ??????????????????????????????????????????????????????
     *
     * @param array $options
     * @return array
     */
    public function getList(array $options = [])
    {
        $options = array_merge([
            'conditions' => []
        ], $options);
        /** @var CustomTable $table */
        $table = $this->CustomEntries->CustomTables->get($this->CustomEntries->tableId);
        $this->CustomEntries->setDisplayField($table->display_field);
        if ($table->has_child) {
            return $this->getParentTargetList(null, $options['conditions']);
        } else {
            return $this->CustomEntries->find('list')->where($options['conditions'])->toArray();
        }
    }

    /**
     * ?????????????????????????????????SQL???????????????
     *
     * @param string $order
     * @param string $direction
     * @return string
     */
    public function createOrder(string $order, string $direction)
    {
        if (strpos($order, '.') === false) {
            $order = "CustomEntries.{$order}";
        }
        return "{$order} {$direction}, CustomEntries.id {$direction}";
    }

    /**
     * ????????????????????????????????????????????????????????????
     *
     * @return EntityInterface
     */
    public function get($id, array $options = [])
    {
        $options = array_merge([
            'contain' => ['CustomTables'],
            'status' => '',
            'use_api' => null,
        ], $options);

        $conditions = [];
        if ($options['status'] === 'publish') {
            $conditions = $this->CustomEntries->getConditionAllowPublish();
        }

        if (is_numeric($id)) {
            $conditions = array_merge_recursive(
                $conditions,
                ['CustomEntries.id' => $id]
            );
        } else {
            $conditions = array_merge_recursive(
                $conditions,
                ['CustomEntries.name' => rawurldecode($id)]
            );
        }

        return $this->CustomEntries->find()
            ->select($this->createSelect($options))
            ->select($this->CustomEntries->CustomTables)
            ->where($conditions)
            ->contain($options['contain'])
            ->first();
    }

    /**
     * select ?????????????????????????????????????????????
     *
     * @param array $options
     * @return array|string[]
     */
    public function createSelect(array $options)
    {
        $schema = $this->CustomEntries->getSchema()->columns();
        $select = array_combine(array_values($schema), array_values($schema));
        if ($options['use_api']) {
            if ($this->CustomEntries->links) {
                foreach($this->CustomEntries->links as $link) {
                    if ($link->use_api && !$link->parent_id) {
                        $select[$link->name] = $link->name;
                    } else {
                        unset($select[$link->name]);
                    }
                }
            }
        }
        $select = array_map(function($v) {
            return 'CustomEntries.' . $v;
        }, $select);
        return array_values($select);
    }

    /**
     * ???????????????????????????????????????????????????
     *
     * @param array $postData
     * @return EntityInterface
     */
    public function create(array $postData)
    {
        $postData = $this->autoConvert($postData);
        $entity = $this->CustomEntries->patchEntity($this->CustomEntries->newEmptyEntity(), $postData);
        return $this->CustomEntries->saveOrFail($entity);
    }

    /**
     * ??????????????????????????????????????????
     *
     * @param EntityInterface $entity
     * @param array $postData
     * @return EntityInterface
     */
    public function update(EntityInterface $entity, array $postData)
    {
        $postData = $this->autoConvert($postData);
        $entity = $this->CustomEntries->patchEntity($entity, $postData);
        return $this->CustomEntries->saveOrFail($entity);
    }

    /**
     * ??????????????????????????????????????????
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        $entity = $this->get($id);
        return $this->CustomEntries->delete($entity);
    }

    /**
     * ????????????????????????????????????????????????????????????
     *
     * @param int $tableId
     * @param string $fieldName
     * @param string $type
     * @return bool
     */
    public function addField(int $tableId, string $fieldName, string $type): bool
    {
        $table = $this->CustomEntries->getTableName($tableId);
        return $this->BcDatabaseService->addColumn($table, $fieldName, $type);
    }

    /**
     * ??????????????????????????????????????????????????????????????????
     *
     * @param int $tableId
     * @param string $oldName
     * @param string $newName
     * @return bool
     */
    public function renameField(int $tableId, string $oldName, string $newName): bool
    {
        $table = $this->CustomEntries->getTableName($tableId);
        return $this->BcDatabaseService->renameColumn($table, $oldName, $newName);
    }

    /**
     * ????????????????????????????????????????????????????????????
     *
     * @param int $tableId
     * @param string $fieldName
     * @return bool
     */
    public function removeField(int $tableId, string $fieldName)
    {
        $table = $this->CustomEntries->getTableName($tableId);
        return $this->BcDatabaseService->removeColumn($table, $fieldName);
    }

    /**
     * ?????????????????????????????????????????????????????????
     *
     * @param int $tableId
     * @return bool
     */
    public function createTable(int $tableId): bool
    {
        $schema = [
            'custom_table_id' => ['type' => 'integer', 'null' => true, 'default' => null],
            'name' => ['type' => 'string', 'limit' => 255, 'null' => true, 'default' => null],
            'title' => ['type' => 'string', 'limit' => 255, 'null' => true, 'default' => null],
            'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null],
            'lft' => ['type' => 'integer', 'null' => true, 'default' => null],
            'rght' => ['type' => 'integer', 'null' => true, 'default' => null],
            'level' => ['type' => 'integer', 'null' => true, 'default' => null],
            'status' => ['type' => 'boolean', 'null' => true, 'default' => false],
            'publish_begin' => ['type' => 'datetime', 'null' => true, 'default' => null],
            'publish_end' => ['type' => 'datetime', 'null' => true, 'default' => null],
            'published' => ['type' => 'datetime', 'null' => true, 'default' => null],
            'creator_id' => ['type' => 'integer', 'null' => true, 'default' => null],
            'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
            'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
        ];
        $table = $this->CustomEntries->getTableName($tableId);
        if ($this->BcDatabaseService->tableExists($table)) {
            $this->BcDatabaseService->dropTable($table);
        }
        return $this->BcDatabaseService->createTable($table, $schema);
    }

    /**
     * ???????????????????????????????????????????????????????????????
     *
     * @param int $tableId
     * @param string $oldName
     * @return bool
     */
    public function renameTable(int $tableId, string $oldName)
    {
        $oldTableName = $this->CustomEntries->getTableName($tableId, $oldName);
        $newTableName = $this->CustomEntries->getTableName($tableId);
        if (!$this->BcDatabaseService->tableExists($oldTableName)) {
            return $this->createTable($tableId);
        }
        if ($oldTableName !== $newTableName) {
            return $this->BcDatabaseService->renameTable($oldTableName, $newTableName);
        }
        return true;
    }

    /**
     * ?????????????????????????????????????????????????????????
     *
     * @param int $tableId
     * @return bool
     */
    public function dropTable(int $tableId)
    {
        $tableName = $this->CustomEntries->getTableName($tableId);
        return $this->BcDatabaseService->dropTable($tableName);
    }

    /**
     * ???????????????????????????????????????
     *
     * @param int $tableId
     * @param array $fields
     */
    public function addFields(int $tableId, array $links)
    {
        $tableName = $this->CustomEntries->getTableName($tableId);
        foreach($links as $link) {
            if ($this->BcDatabaseService->columnExists($tableName, $link->name)) continue;
            $field = $this->CustomTables->CustomLinks->CustomFields->get($link->custom_field_id);
            $columnType = CustomContentUtil::getPluginSetting($field->type, 'columnType');
            if (!$columnType) $columnType = 'text';
            $this->BcDatabaseService->addColumn($tableName, $link->name, $columnType);
        }
    }

    /**
     * ????????????????????????????????????
     *
     * @param string $field
     * @return array
     */
    public function getControlSource(string $field, array $options = []): array
    {
        if ($field === 'creator_id') {
            /** @var UsersService $usersService */
            $usersService = $this->getService(UsersServiceInterface::class);
            return $usersService->getList($options);
        } elseif ($field === 'parent_id') {
            return $this->getParentTargetList(
                isset($options['selfId'])? $options['selfId'] : null
            );
        }
        return [];
    }

    /**
     * ????????????????????????????????????????????????????????????
     *
     * @param int|null $selfId
     * @return array
     */
    public function getParentTargetList($selfId, array $options = [])
    {
        $conditions = (!empty($options['conditions']))? $options['conditions'] : [];
        if ($selfId) {
            $conditions = ['CustomEntries.id NOT IN' => $selfId];
        }
        $parentsSrc = $this->CustomEntries->find('treeList')
            ->where($conditions)
            ->order(['lft'])
            ->all();
        $parents = [];
        foreach($parentsSrc as $key => $value) {
            if (preg_match("/^([_]+)/i", $value, $matches)) {
                $value = preg_replace("/^[_]+/i", '', $value);
                $prefix = str_replace('_', '?????????', $matches[1]);
                $value = $prefix . '???' . $value;
            }
            $parents[$key] = $value;
        }
        return $parents;
    }

    /**
     * ???????????????????????????????????????????????????????????????????????????
     *
     * @param EntityInterface $entity
     * @return bool
     */
    public function isAllowPublish(EntityInterface $entity)
    {
        $allowPublish = $entity->status;
        // ???????????????????????????????????????????????????????????????????????????????????????????????????
        $invalidBegin = $entity->publish_begin instanceof FrozenTime && $entity->publish_begin->isFuture();
        $invalidEnd = $entity->publish_end instanceof FrozenTime && $entity->publish_end->isPast();
        if ($invalidBegin || $invalidEnd) {
            $allowPublish = false;
        }
        return $allowPublish;
    }

    /**
     * ?????????????????????????????? URL ???????????????
     *
     * @param Content $content
     * @param EntityInterface $entity
     * @param bool $full
     * @return string
     */
    public function getUrl(Content $content, EntityInterface $entity, bool $full = true)
    {
        /** @var SitesServiceInterface $sitesService */
        $sitesService = $this->getService(SitesServiceInterface::class);
        $site = $sitesService->findByUrl($content->url);

        /** @var ContentsServiceInterface $contentsService */
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $contentUrl = $contentsService->getUrl(rawurldecode($content->url), $full, !empty($site->use_subdomain), false);
        $id = ($entity->name)?: $entity->id;
        return $contentUrl . 'view/' . $id;
    }

    /**
     * ????????????
     * ??????????????????????????????????????????????????????????????????????????????
     * ?????????????????????????????????
     *
     * @param array $data
     * @return array $data
     * @checked
     * @noTodo
     */
    public function autoConvert(array $data): array
    {
        foreach($this->CustomEntries->links as $link) {
            /** @var CustomLink $link */
            if (empty($data[$link->name])) continue;
            $value = $data[$link->name];
            // ????????????
            if ($link->custom_field->auto_convert === 'CONVERT_HANKAKU') {
                $value = mb_convert_kana($value, 'a');
            }
            // ????????????
            if ($link->custom_field->auto_convert === 'CONVERT_ZENKAKU') {
                $value = mb_convert_kana($value, 'AK');
            }
            $data[$link->name] = $value;
        }
        return $data;
    }

    /**
     * ??????????????????????????????????????????
     *
     * @param int $id
     * @return mixed
     */
    public function moveUp(int $id)
    {
        return $this->CustomEntries->moveUp($this->get($id));
    }

    /**
     * ??????????????????????????????????????????
     *
     * @param int $id
     * @return mixed
     */
    public function moveDown(int $id)
    {
        return $this->CustomEntries->moveDown($this->get($id));
    }

}

<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * CustomFields seed.
 */
class CustomFieldsSeed extends BcSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 8,
                'title' => '求人分類',
                'name' => 'recruit_category',
                'type' => 'BcCcRelated',
                'status' => 1,
                'default_value' => '新卒採用',
                'validate' => '',
                'regex' => '',
                'regex_error_message' => '',
                'counter' => 0,
                'auto_convert' => '',
                'placeholder' => '',
                'size' => NULL,
                'max_length' => NULL,
                'source' => '',
                'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"","height":"","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":"","max_file_size":"","file_ext":""}}',
                'created' => '2023-01-30 06:22:47',
                'modified' => '2023-02-20 11:18:32',
                'line' => NULL,
            ],
            [
                'id' => 9,
                'title' => 'この仕事の特徴',
                'name' => 'feature',
                'type' => 'BcCcMultiple',
                'status' => 1,
                'default_value' => '',
                'validate' => '',
                'regex' => '',
                'regex_error_message' => '',
                'counter' => 0,
                'auto_convert' => '',
                'placeholder' => '',
                'size' => NULL,
                'max_length' => NULL,
                'source' => '経験者優遇
土日祝日休み
交通費支給
社会保険あり
研修あり
昇給あり
資格取得支援
職場内禁煙',
                'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"","height":"","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":"","max_file_size":"","file_ext":""}}',
                'created' => '2023-01-30 06:23:41',
                'modified' => '2023-02-20 11:21:03',
                'line' => NULL,
            ],
            [
                'id' => 10,
                'title' => 'メインビジュアル',
                'name' => 'main_visual',
                'type' => 'BcCcFile',
                'status' => 1,
                'default_value' => '',
                'validate' => '["MAX_FILE_SIZE","FILE_EXT"]',
                'regex' => '',
                'regex_error_message' => '',
                'counter' => 0,
                'auto_convert' => '',
                'placeholder' => '',
                'size' => NULL,
                'max_length' => NULL,
                'source' => '',
                'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"","height":"","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":"","max_file_size":"20","file_ext":"jpg,png,gif"}}',
                'created' => '2023-01-30 06:25:10',
                'modified' => '2023-02-08 13:52:21',
                'line' => NULL,
            ],
            [
                'id' => 11,
                'title' => 'Wysiwyシンプル',
                'name' => 'wysiwyg_simple',
                'type' => 'BcCcWysiwyg',
                'status' => 1,
                'default_value' => '',
                'validate' => '',
                'regex' => '',
                'regex_error_message' => '',
                'counter' => 0,
                'auto_convert' => '',
                'placeholder' => '',
                'size' => NULL,
                'max_length' => NULL,
                'source' => '',
                'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"100%","height":"150px","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":"","max_file_size":"","file_ext":""}}',
                'created' => '2023-01-30 06:26:52',
                'modified' => '2023-02-12 23:14:42',
                'line' => NULL,
            ],
            [
                'id' => 14,
                'title' => '会社紹介資料',
                'name' => 'company_introduction',
                'type' => 'BcCcText',
                'status' => 1,
                'default_value' => NULL,
                'validate' => NULL,
                'regex' => NULL,
                'regex_error_message' => NULL,
                'counter' => NULL,
                'auto_convert' => NULL,
                'placeholder' => NULL,
                'size' => 60,
                'max_length' => NULL,
                'source' => NULL,
                'meta' => NULL,
                'created' => '2023-01-30 06:30:21',
                'modified' => '2023-01-30 06:30:21',
                'line' => NULL,
            ],
            [
                'id' => 15,
                'title' => 'テキスト60',
                'name' => 'text_60',
                'type' => 'BcCcText',
                'status' => 1,
                'default_value' => '',
                'validate' => '',
                'regex' => '',
                'regex_error_message' => '',
                'counter' => 0,
                'auto_convert' => '',
                'placeholder' => '',
                'size' => 60,
                'max_length' => NULL,
                'source' => '',
                'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"","height":"","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":"","max_file_size":"","file_ext":""}}',
                'created' => '2023-01-30 06:31:21',
                'modified' => '2023-02-13 19:55:16',
                'line' => NULL,
            ],
            [
                'id' => 16,
                'title' => '雇用形態',
                'name' => 'employment_status',
                'type' => 'BcCcRadio',
                'status' => 1,
                'default_value' => '正社員',
                'validate' => NULL,
                'regex' => NULL,
                'regex_error_message' => NULL,
                'counter' => NULL,
                'auto_convert' => NULL,
                'placeholder' => NULL,
                'size' => NULL,
                'max_length' => NULL,
                'source' => '正社員
派遣
アルバイト',
                'meta' => NULL,
                'created' => '2023-01-30 06:32:00',
                'modified' => '2023-02-04 21:21:45',
                'line' => NULL,
            ],
            [
                'id' => 17,
                'title' => 'テキストエリア（大）',
                'name' => 'textarea_large',
                'type' => 'BcCcTextarea',
                'status' => 1,
                'default_value' => '',
                'validate' => '',
                'regex' => '',
                'regex_error_message' => '',
                'counter' => 0,
                'auto_convert' => '',
                'placeholder' => '',
                'size' => NULL,
                'max_length' => NULL,
                'source' => '',
                'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"","height":"","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":"","max_file_size":"","file_ext":""}}',
                'created' => '2023-01-30 06:32:49',
                'modified' => '2023-02-20 11:16:48',
                'line' => 12,
            ],
            [
                'id' => 18,
                'title' => 'テキストエリア（小）',
                'name' => 'textarea_small',
                'type' => 'BcCcTextarea',
                'status' => 1,
                'default_value' => '',
                'validate' => '',
                'regex' => '',
                'regex_error_message' => '',
                'counter' => 0,
                'auto_convert' => '',
                'placeholder' => '',
                'size' => NULL,
                'max_length' => NULL,
                'source' => '',
                'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"","height":"","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":"","max_file_size":"","file_ext":""}}',
                'created' => '2023-01-30 06:33:56',
                'modified' => '2023-02-14 13:25:03',
                'line' => 2,
            ],
            [
                'id' => 19,
                'title' => 'テキストノーマル',
                'name' => 'text_normal',
                'type' => 'BcCcText',
                'status' => 1,
                'default_value' => '',
                'validate' => '',
                'regex' => '',
                'regex_error_message' => '',
                'counter' => 0,
                'auto_convert' => '',
                'placeholder' => '',
                'size' => NULL,
                'max_length' => NULL,
                'source' => '',
                'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"","height":"","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":"","max_file_size":"","file_ext":""}}',
                'created' => '2023-01-30 06:34:30',
                'modified' => '2023-02-12 23:43:54',
                'line' => NULL,
            ],
            [
                'id' => 25,
                'title' => 'グループ',
                'name' => 'group',
                'type' => 'group',
                'status' => 1,
                'default_value' => '',
                'validate' => '',
                'regex' => '',
                'regex_error_message' => '',
                'counter' => 0,
                'auto_convert' => '',
                'placeholder' => '',
                'size' => NULL,
                'max_length' => NULL,
                'source' => '',
                'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"","height":"","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":"","max_file_size":"","file_ext":""}}',
                'created' => '2023-01-30 06:37:56',
                'modified' => '2023-02-20 10:15:53',
                'line' => NULL,
            ],
            [
                'id' => 33,
                'title' => 'テキストエリア（中）',
                'name' => 'textarea_midium',
                'type' => 'BcCcTextarea',
                'status' => 1,
                'default_value' => '',
                'validate' => '',
                'regex' => '',
                'regex_error_message' => '',
                'counter' => 0,
                'auto_convert' => '',
                'placeholder' => '',
                'size' => NULL,
                'max_length' => NULL,
                'source' => '',
                'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"","height":"","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":"","max_file_size":"","file_ext":""}}',
                'created' => '2023-01-30 09:54:20',
                'modified' => '2023-02-12 23:34:39',
                'line' => 6,
            ],
            [
                'id' => 34,
                'title' => '基本給支給タイプ',
                'name' => 'salary_type',
                'type' => 'BcCcRadio',
                'status' => 1,
                'default_value' => '月給',
                'validate' => NULL,
                'regex' => NULL,
                'regex_error_message' => NULL,
                'counter' => NULL,
                'auto_convert' => NULL,
                'placeholder' => NULL,
                'size' => NULL,
                'max_length' => NULL,
                'source' => '月給
日給
時給

',
                'meta' => NULL,
                'created' => '2023-01-30 10:18:59',
                'modified' => '2023-02-04 21:24:09',
                'line' => NULL,
            ],
            [
                'id' => 39,
                'title' => 'テキスト10',
                'name' => 'text_10',
                'type' => 'BcCcText',
                'status' => 1,
                'default_value' => '',
                'validate' => '',
                'regex' => '',
                'regex_error_message' => '',
                'counter' => 0,
                'auto_convert' => '',
                'placeholder' => '',
                'size' => 10,
                'max_length' => NULL,
                'source' => '',
                'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"","height":"","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":"","max_file_size":"","file_ext":""}}',
                'created' => '2023-01-30 10:28:25',
                'modified' => '2023-02-20 18:13:02',
                'line' => NULL,
            ],
            [
                'id' => 40,
                'title' => '都道府県',
                'name' => 'pref',
                'type' => 'BcCcPref',
                'status' => 1,
                'default_value' => '',
                'validate' => '',
                'regex' => '',
                'regex_error_message' => '',
                'counter' => 0,
                'auto_convert' => '',
                'placeholder' => '',
                'size' => NULL,
                'max_length' => NULL,
                'source' => '',
                'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"","height":"","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":""}}',
                'created' => '2023-01-30 10:29:01',
                'modified' => '2023-02-20 11:18:22',
                'line' => NULL,
            ],
        ];

        $table = $this->table('custom_fields');
        $table->insert($data)->save();
    }
}

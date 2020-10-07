<?php

/**
 * [Controller] CuCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			CuCustomField
 * @license			MIT
 */
App::uses('CuCustomFieldApp', 'CuCustomField.Controller');

class CuCustomFieldConfigsController extends CuCustomFieldAppController
{

	/**
	 * ControllerName
	 *
	 * @var string
	 */
	public $name = 'CuCustomFieldConfigs';

	/**
	 * Model
	 *
	 * @var array
	 */
	public $uses = array('CuCustomField.CuCustomFieldConfig', 'CuCustomField.CuCustomFieldValue');

	/**
	 * ぱんくずナビ
	 *
	 * @var string
	 */
	public $crumbs = array(
		array('name' => 'プラグイン管理', 'url' => array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')),
		array('name' => 'プチ・カスタムフィールド設定管理', 'url' => array('plugin' => 'cu_custom_field', 'controller' => 'cu_custom_field_configs', 'action' => 'index'))
	);

	/**
	 * 管理画面タイトル
	 *
	 * @var string
	 */
	public $adminTitle = 'プチ・カスタムフィールド設定';

	/**
	 * beforeFilter
	 *
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
	}

	/**
	 * [ADMIN] プチ・カスタムフィールド設定一覧
	 *
	 */
	public function admin_index()
	{
		$this->pageTitle = $this->adminTitle . '一覧';
		$this->search	 = 'cu_custom_field_configs_index';
		$this->help		 = 'cu_custom_field_configs_index';

		$default = array(
			'named' => array(
				'num'		 => $this->siteConfigs['admin_list_num'],
				'sortmode'	 => 0));
		$this->setViewConditions('CuCustomFieldConfig', array('default' => $default));

		$conditions		 = $this->_createAdminIndexConditions($this->request->data);
		$this->paginate	 = array(
			'conditions' => $conditions,
			'fields'	 => array(),
			'limit'		 => $this->passedArgs['num']
		);

		$this->set('datas', $this->paginate('CuCustomFieldConfig'));
		$this->set('blogContentDatas', array('0' => '指定しない') + $this->blogContentDatas);
	}

	/**
	 * [ADMIN] 編集
	 *
	 * @param int $id
	 */
	public function admin_edit($id = null)
	{
		$this->pageTitle = $this->adminTitle . '編集';

		parent::admin_edit($id);
	}

	/**
	 * [ADMIN] 新規登録
	 *
	 */
	public function admin_add()
	{
		$this->pageTitle = $this->adminTitle . '追加';

		if ($this->request->is('post')) {
			if ($this->{$this->modelClass}->save($this->request->data)) {
				$message = $this->name . 'を追加しました。';
				$this->setMessage($message, false, true);
				$this->redirect(array('action' => 'index'));
			} else {
				$this->setMessage('入力エラーです。内容を修正して下さい。', true);
			}
		} else {
			$this->request->data							 = $this->{$this->modelClass}->getDefaultValue();
			$this->request->data[$this->modelClass]['model'] = 'BlogContent';
		}

		// 設定データがあるブログは選択リストから除外する
		$dataList = $this->{$this->modelClass}->find('all');
		if ($dataList) {
			foreach ($dataList as $data) {
				unset($this->blogContentDatas[$data[$this->modelClass]['content_id']]);
			}
		}

		$this->set('blogContentDatas', $this->blogContentDatas);
		$this->render('form');
	}

	/**
	 * [ADMIN] 削除
	 *
	 * @param int $id
	 */
	public function admin_delete($id = null)
	{
		parent::admin_delete($id);
	}

	/**
	 * 各ブログ別のプチ・カスタムフィールド設定データを作成する
	 * - プチ・カスタムフィールド設定データがないブログ用のデータのみ作成する
	 *
	 */
	public function admin_first()
	{
		$this->pageTitle = $this->adminTitle . 'データ作成';

		if ($this->request->data) {
			$count = 0;
			if ($this->blogContentDatas) {
				foreach ($this->blogContentDatas as $key => $blog) {

					$configData = $this->CuCustomFieldConfig->findByContentId($key);
					if (!$configData) {
						$this->request->data['CuCustomFieldConfig']['content_id'] = $key;
						$this->request->data['CuCustomFieldConfig']['status']	 = true;
						$this->request->data['CuCustomFieldConfig']['model']		 = 'BlogContent';
						$this->request->data['CuCustomFieldConfig']['form_place'] = 'normal';
						$this->CuCustomFieldConfig->create($this->request->data);
						if (!$this->CuCustomFieldConfig->save($this->request->data, false)) {
							$this->log(sprintf('ブログID：%s の登録に失敗しました。', $key));
						} else {
							$count++;
						}
					}
				}
			}
			$message = sprintf('%s 件のプチ・カスタムフィールド設定を登録しました。', $count);
			$this->setMessage($message);
			$this->redirect(array('controller' => 'cu_custom_field_configs', 'action' => 'index'));
		}
	}

	/**
	 * 一覧用の検索条件を生成する
	 *
	 * @param array $data
	 * @return array $conditions
	 */
	protected function _createAdminIndexConditions($data)
	{
		$conditions		 = array();
		$blogContentId	 = '';

		if (isset($data['CuCustomFieldConfig']['content_id'])) {
			$blogContentId = $data['CuCustomFieldConfig']['content_id'];
		}

		unset($data['_Token']);
		unset($data['CuCustomFieldConfig']['content_id']);

		// 条件指定のないフィールドを解除
		if (!empty($data['CuCustomFieldConfig'])) {
			foreach ($data['CuCustomFieldConfig'] as $key => $value) {
				if ($value === '') {
					unset($data['CuCustomFieldConfig'][$key]);
				}
			}
			if ($data['CuCustomFieldConfig']) {
				$conditions = $this->postConditions($data);
			}
		}

		if ($blogContentId) {
			$conditions = array(
				'CuCustomFieldConfig.content_id' => $blogContentId
			);
		}

		if ($conditions) {
			return $conditions;
		} else {
			return array();
		}
	}

}

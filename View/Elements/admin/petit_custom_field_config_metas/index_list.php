<?php
/**
 * [ADMIN] PetitCustomField
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			PetitCustomField
 * @license			MIT
 */
?>
<?php $this->BcBaser->element('pagination') ?>

<h3>
<?php $this->BcBaser->link($this->BcText->arrayValue($contentId, $blogContentDatas) .' ブログ設定編集はこちら', array(
	'admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents',
	'action' => 'edit', $contentId
)) ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<?php $this->BcBaser->link('≫記事一覧こちら', array(
	'admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts',
	'action' => 'index', $contentId
)) ?>
</h3>

<table cellpadding="0" cellspacing="0" class="list-table sort-table" id="ListTable">
	<thead>
		<tr>
			<th class="list-tool" style="width: 50px;">
				<div>
					<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', array('width' => 69, 'height' => 18, 'alt' => '新規追加', 'class' => 'btn')),
							array('controller' => 'petit_custom_field_config_fields', 'action' => 'add', $configId)) ?>
				</div>
			</th>
			<th><?php echo $this->Paginator->sort('id', array(
					'asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' NO',
					'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' NO'),
					array('escape' => false, 'class' => 'btn-direction')) ?>
			</th>
			<th>
				フィールド名
			</th>
			<th>
				ラベル名
			</th>
			<th>
				フィールドタイプ
			</th>
		</tr>
	</thead>
	<tbody>
<?php if(!empty($datas)): ?>
	<?php foreach ($datas as $key => $data): ?>
		<?php $this->BcBaser->element('petit_custom_field_config_metas/index_row', array('data' => $data, 'count' => ($key + 1))) ?>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="5"><p class="no-data">データがありません。</p></td>
	</tr>
<?php endif; ?>
	</tbody>
</table>

<!-- list-num -->
<?php $this->BcBaser->element('list_num') ?>

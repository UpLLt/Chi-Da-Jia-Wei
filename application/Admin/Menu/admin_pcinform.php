<?php
return array (
  'app' => 'Admin',
  'model' => 'PCInform',
  'action' => 'default',
  'data' => '',
  'type' => '1',
  'status' => '1',
  'name' => 'PC管理',
  'icon' => '',
  'remark' => '',
  'listorder' => '0',
  'children' => 
  array (
    array (
      'app' => 'Admin',
      'model' => 'PCInform',
      'action' => 'a',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '商家管理',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
      'children' => 
      array (
        array (
          'app' => 'Admin',
          'model' => 'PCInform',
          'action' => 'must_read',
          'data' => '',
          'type' => '1',
          'status' => '1',
          'name' => '发单必读',
          'icon' => '',
          'remark' => '',
          'listorder' => '0',
        ),
      ),
    ),
    array (
      'app' => 'Admin',
      'model' => 'PCInform',
      'action' => 'b',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '平台首页',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
      'children' => 
      array (
        array (
          'app' => 'Admin',
          'model' => 'Inform',
          'action' => 'info_list',
          'data' => '',
          'type' => '1',
          'status' => '1',
          'name' => '推送消息',
          'icon' => '',
          'remark' => '',
          'listorder' => '0',
        ),
        array (
          'app' => 'Admin',
          'model' => 'PCInform',
          'action' => 'index_list',
          'data' => '',
          'type' => '1',
          'status' => '1',
          'name' => '展示图片',
          'icon' => '',
          'remark' => '',
          'listorder' => '0',
        ),
        array (
          'app' => 'Portal',
          'model' => 'AdminPost',
          'action' => 'index',
          'data' => '',
          'type' => '1',
          'status' => '1',
          'name' => '文章管理',
          'icon' => '',
          'remark' => '',
          'listorder' => '1',
          'children' => 
          array (
            array (
              'app' => 'Portal',
              'model' => 'AdminPost',
              'action' => 'listorders',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '文章排序',
              'icon' => '',
              'remark' => '',
              'listorder' => '0',
            ),
            array (
              'app' => 'Portal',
              'model' => 'AdminPost',
              'action' => 'top',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '文章置顶',
              'icon' => '',
              'remark' => '',
              'listorder' => '0',
            ),
            array (
              'app' => 'Portal',
              'model' => 'AdminPost',
              'action' => 'recommend',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '文章推荐',
              'icon' => '',
              'remark' => '',
              'listorder' => '0',
            ),
            array (
              'app' => 'Portal',
              'model' => 'AdminPost',
              'action' => 'move',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '批量移动',
              'icon' => '',
              'remark' => '',
              'listorder' => '1000',
            ),
            array (
              'app' => 'Portal',
              'model' => 'AdminPost',
              'action' => 'check',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '文章审核',
              'icon' => '',
              'remark' => '',
              'listorder' => '1000',
            ),
            array (
              'app' => 'Portal',
              'model' => 'AdminPost',
              'action' => 'delete',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '删除文章',
              'icon' => '',
              'remark' => '',
              'listorder' => '1000',
            ),
            array (
              'app' => 'Portal',
              'model' => 'AdminPost',
              'action' => 'edit',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '编辑文章',
              'icon' => '',
              'remark' => '',
              'listorder' => '1000',
              'children' => 
              array (
                array (
                  'app' => 'Portal',
                  'model' => 'AdminPost',
                  'action' => 'edit_post',
                  'data' => '',
                  'type' => '1',
                  'status' => '0',
                  'name' => '提交编辑',
                  'icon' => '',
                  'remark' => '',
                  'listorder' => '0',
                ),
              ),
            ),
            array (
              'app' => 'Portal',
              'model' => 'AdminPost',
              'action' => 'add',
              'data' => '',
              'type' => '1',
              'status' => '0',
              'name' => '添加文章',
              'icon' => '',
              'remark' => '',
              'listorder' => '1000',
              'children' => 
              array (
                array (
                  'app' => 'Portal',
                  'model' => 'AdminPost',
                  'action' => 'add_post',
                  'data' => '',
                  'type' => '1',
                  'status' => '0',
                  'name' => '提交添加',
                  'icon' => '',
                  'remark' => '',
                  'listorder' => '0',
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    array (
      'app' => 'Admin',
      'model' => 'PCInform',
      'action' => 'c',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '服务中心',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
      'children' => 
      array (
        array (
          'app' => 'Admin',
          'model' => 'Policy',
          'action' => 'index',
          'data' => '',
          'type' => '1',
          'status' => '1',
          'name' => '政策列表',
          'icon' => '',
          'remark' => '',
          'listorder' => '0',
        ),
      ),
    ),
  ),
);
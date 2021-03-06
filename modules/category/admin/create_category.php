<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2020 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sat, 31 Oct 2020 02:20:33 GMT
 */

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

$page_title = $lang_module['create_category'];

//------------------------------
// Viết code xử lý chung vào đây

// =========== Insert dữ liệu ============= //
$post = [];
$error = [];
$post['id'] = $nv_Request->get_int('id', 'post,get', 0);
$post['name'] = $nv_Request->get_title('name', 'post', '');
$post['description'] = $nv_Request->get_textarea('description', 'post', '', NV_ALLOWED_HTML_TAGS);
$post['submit'] = $nv_Request->get_title('submit', 'post', '');

if (!empty($post['submit'])) {

    if (empty($post['name'])) {
        $error[] = $lang_module['name'];
    }

    if (empty($error)) {
        if ($post['id'] > 0) {
            // update
            $sql = "UPDATE `shop_category` SET
            name=:name,description=:description WHERE id = " . $post['id'];
            $s = $db->prepare($sql);
        } else {
            //  Insert
            $sql = "INSERT INTO `shop_category`
            (`name`,`description`,`weight`) 
            VALUES (:name, :description, :weight)";
            $s = $db->prepare($sql);
            $s->bindValue('weight', 1);
        }
        $s->bindParam('name', $post['name']);
        $s->bindParam('description', $post['description']);
        $exe = $s->execute();

        if ($post['id'] > 0) {
            $error[] = "Update ok!";
        } else {
            $error[] = "Insert ok !";
        }
    }
}
// ================================================== //

// ============= Edit dữ liệu ============ //
if ($post['id'] > 0) {

    $sql = "SELECT * FROM `shop_category` WHERE id = "  . $post['id'];
    $post =  $db->query($sql)->fetch();
}

// ====================================== //




//------------------------------

$xtpl = new XTemplate('create_category.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('Error', implode('<br>', $error));
$xtpl->assign('POST', $post);
//-------------------------------
// Viết code xuất ra site vào đây
//-------------------------------
if (!empty($error)) {
    $xtpl->parse('main.error');
}
$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
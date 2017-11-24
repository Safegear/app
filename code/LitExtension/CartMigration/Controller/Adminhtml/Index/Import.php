<?php
/**
 * @project: CartMigration
 * @author : LitExtension
 * @url    : http://litextension.com
 * @email  : litextension@gmail.com
 */

namespace LitExtension\CartMigration\Controller\Adminhtml\Index;

use Zend\Json\Json;

class Import extends \LitExtension\CartMigration\Controller\Adminhtml\Index\Index
{

    /**
     * Show admin gui
     */
    public function execute(){
        $params = $this->getRequest()->getParams();
        if(isset($params['action']) && $params['action'] != ''){
            $action = $params['action'];
            if(in_array($action, $this->_import_action)){
                $this->_import($action);
            } else {
                $function = '_'.$action;
                $this->$function();
            }
        } else {
            $this->_redirect('admin/lecm/index');
        }
        return ;
    }

    protected function _recentData()
    {
        $response = $this->_defaultResponse();
        $router = $this->_objectManager->create('LitExtension\CartMigration\Model\Cart');
        $recent = $router->selectTable('lecm_recent');
        $result = array();
        $this->_view->loadLayout();
        foreach($recent as $row){
            $id = $row['id'];
            $notice = unserialize($row['notice']);
            $cart_type = $notice['config']['cart_type'];
            $cart_version = $notice['config']['cart_version'];
            $model_name = "LitExtension\CartMigration\Model\\" . $router->getCart($cart_type, $cart_version);
            $cart = $this->_objectManager->create($model_name);
            $cart->setNotice($notice);
            $cart->checkRecent();
            $info = $cart->getNotice();
            $elm = "#refresh-recent-" . $id;
            $block = $this->_view->getLayout()->createBlock('LitExtension\CartMigration\Block\Adminhtml\Index', 'lecm.recent.new' . $id)->setTemplate('recent/new.phtml');
            $html = "";
            if($block){
                $html = $block->setNotice($info)->toHtml();
            }
            $result[] = array(
                'elm' => $elm,
                'html' => $html,
            );
        }
        $response['result'] = 'success';
        $response['data'] = $result;
        $this->_responseAjaxJson($response);
        return;
    }

    /**
     * Show display to success step setup in admin gui
     */
    protected function _setup(){
        $response = array();
        $router = $this->_objectManager->create('LitExtension\CartMigration\Model\Cart');
        $del = $this->_deleteNotice($router);
        if(!$del){
            return $this->_responseAjaxJson($router->errorDatabase());
        }
        $this->_notice = $this->_getNotice($router);
        $params = $this->getRequest()->getParams();
        $this->_notice['config']['cart_type'] = $params['cart_type'];
        $this->_notice['config']['cart_url'] = trim(rtrim($params['cart_url'], '/'));
        $this->_notice['config']['cart_token'] = trim($params['cart_token']);
        $router->setNotice($this->_notice);
        $check = $router->checkConnector();
        if($check['result'] !=  'success'){
            return $this->_responseAjaxJson($check);
        }
        $this->_notice = $router->getNotice();
        $model = "LitExtension\CartMigration\Model\\" . $router->getCart($this->_notice['config']['cart_type'], $this->_notice['config']['cart_version']);
        $this->_cart = $this->_objectManager->create($model);
        $this->_cart->setNotice($this->_notice);
        $result = $this->_cart->displayConfig();
        if($result['result'] != 'success'){
            return $this->_responseAjaxJson($result);
        }
        $this->_notice = $this->_cart->getNotice();
        $this->_view->loadLayout();
        $block = $this->_view->getLayout()->createBlock('LitExtension\CartMigration\Block\Adminhtml\Index', 'lecm.config')->setTemplate('config.phtml');
        $html = "";
        if($block){
            $html = $block->setNotice($this->_notice)->toHtml();
        }
        $response['result'] = 'success';
        $response['html'] = $html;
        $save = $this->_saveNotice();
        if(!$save){
            return $this->_responseAjaxJson($router->errorDatabase());
        }
        try{
            $this->_objectManager->create('LitExtension\Core\Helper\Data')->saveConfig('lecupd/general/type', $this->_notice['config']['cart_type']);
        }catch (\Exception $e){}
        return $this->_responseAjaxJson($response);
    }

    /**
     * Show display to success step config in admin gui
     */
    protected function _config(){
        $response = array();
        $this->_initCart();
        $params = $this->getRequest()->getParams();
        $result = $this->_cart->displayConfirm($params);
        if($result['result'] != 'success'){
            return $this->_responseAjaxJson($result);
        }
        $this->_notice = $this->_cart->getNotice();
        $this->_view->loadLayout();
        $block = $this->_view->getLayout()->createBlock('LitExtension\CartMigration\Block\Adminhtml\Index', 'lecm.confirm')->setTemplate('confirm.phtml');
        $html = "";
        if($block){
            $html = $block->setNotice($this->_notice)->toHtml();
        }
        $response['result'] = 'success';
        $response['html'] = $html;
        $save = $this->_saveNotice();
        if(!$save){
            return $this->_responseAjaxJson($this->_cart->errorDatabase());
        }
        return $this->_responseAjaxJson($response);
    }

    /**
     * Show display to success step confirm in admin gui
     */
    protected function _confirm(){
        $this->_initCart();
        $response = array();
        $result = $this->_cart->displayImport();
        if($result['result'] != 'success'){
            return $this->_responseAjaxJson($result);
        }
        $this->_notice = $this->_cart->getNotice();
        if($this->_notice['config']['add_option']['clear_data']){
            $msg = $this->_cart->consoleSuccess("Clearing store ...");
        } else {
            $msg = $this->_cart->getMsgStartImport('taxes');
        }
        $this->_notice['msg_start'] = $msg;
        $this->_view->loadLayout();
        $block = $this->_view->getLayout()->createBlock('LitExtension\CartMigration\Block\Adminhtml\Index', 'lecm.import')->setTemplate('import.phtml');
        $html = "";
        if($block){
            $html = $block->setNotice($this->_notice)->toHtml();
        }
        $response['result'] = 'success';
        $response['html'] = $html;
        $save = $this->_saveNotice();
        if(!$save){
            return $this->_responseAjaxJson($this->_cart->errorDatabase());
        }
        return $this->_responseAjaxJson($response);
    }

    /**
     * Show display to success resume config in admin gui
     */
    protected function _resume(){
        $response = array();
        $this->_initCart();
        $this->_notice['msg_start'] = $this->_cart->consoleSuccess("Resuming ...");
        $this->_view->loadLayout();
        $block = $this->_view->getLayout()->createBlock('LitExtension\CartMigration\Block\Adminhtml\Index', 'lecm.import')->setTemplate('import.phtml');
        $html = "";
        if($block){
            $html = $block->setNotice($this->_notice)->toHtml();
        }
        $response['result'] = 'success';
        $response['html'] = $html;
        $this->_notice['setting'] = $this->_scopeConfig->getValue('lecm/general');
        $save = $this->_saveNotice();
        if(!$save){
            return $this->_responseAjaxJson($this->_cart->errorDatabase());
        }
        return $this->_responseAjaxJson($response);
    }

    protected function _recent()
    {
        $response = array();
        $recent_id = $this->getRequest()->getParam('recent_id', 0);
        $router = $this->_objectManager->create('LitExtension\CartMigration\Model\Cart');
        $recent = $router->selectTableRow('lecm_recent', array('id' => $recent_id));
        if(!$recent){
            $response['result'] = 'error';
            $response['msg'] = 'Recent data not available.';
            $this->_responseAjaxJson($response);
            return;
        }
        $notice = unserialize($recent['notice']);
        $this->_notice = $notice;
        $cart_type = $this->_notice['config']['cart_type'];
        $cart_version = $this->_notice['config']['cart_version'];
        $model = "LitExtension\CartMigration\Model\\" . $router->getCart($cart_type, $cart_version);
        $this->_cart = $this->_objectManager->create($model);
        $this->_notice['config']['add_option']['clear_data'] = false;
        $this->_notice['config']['add_option']['add_new'] = true;
        $this->_cart->setNotice($this->_notice);
        $result = $this->_cart->displayImport();
        if($result['result'] != 'success'){
            $this->_responseAjaxJson($result);
            return;
        }
        $this->_notice = $this->_cart->getNotice();
        $this->_notice['msg_start'] = $this->_cart->consoleSuccess("Recent data ...");
        $this->_view->loadLayout();
        $block = $this->_view->getLayout()->createBlock('LitExtension\CartMigration\Block\Adminhtml\Index', 'lecm.import')->setTemplate('import.phtml');
        $html = "";
        if($block){
            $html = $block->setNotice($this->_notice)->toHtml();
        }
        $response['result'] = 'success';
        $response['html'] = $html;
        $this->_notice['setting'] = $this->_scopeConfig->getValue('lecm/general');
        $save = $this->_saveNotice();
        if(!$save){
            $this->_responseAjaxJson($this->_cart->errorDatabase());
            return;
        }
        $this->_responseAjaxJson($response);
        return;
    }

    /**
     * Process action clear store
     */
    protected function _clear(){
        $this->_initCart();
        $response = $this->_cart->clearStore();
        $this->_notice = $this->_cart->getNotice();
        $this->_notice['fn_resume'] = 'clearStore';
        $save = $this->_saveNotice();
        if(!$save){
            return $this->_responseAjaxJson($this->_cart->errorDatabase(true));
        }
        return $this->_responseAjaxJson($response);
    }

    /**
     * Process config currencies
     */
    protected function _currencies(){
        $this->_initCart();
        $this->_cart->configCurrency();
        $this->_notice = $this->_cart->getNotice();
        if($this->_notice['config']['import']['taxes']){
            $this->_cart->prepareImportTaxes();
        }
        $this->_notice['taxes']['time_start'] = time();
        $this->_notice['fn_resume'] = 'importTaxes';
        $save_user = $this->_saveNotice();
        if(!$save_user){
            $response = $this->_cart->errorDatabase();
            return $this->_responseAjaxJson($response);
        }
        $response = array('result' => 'success');
        return $this->_responseAjaxJson($response);
    }

    /**
     * Process import by action
     */
    protected function _import($action){
        $this->_initCart();
        $response = $this->_defaultResponse();
        $this->_notice['is_running'] = true;
        if(!$this->_notice['config']['import'][$action]){
            $next_action = $this->_next_action[$action];
            if($next_action && $this->_notice['config']['import'][$next_action]){
                $prepare_next = 'prepareImport' . ucfirst($next_action);
                $this->_cart->$prepare_next();
                $this->_notice[$next_action]['time_start'] = time();
            }
            if($next_action){
                $fn_resume = 'import' . ucfirst($next_action);
                $this->_notice['fn_resume'] = $fn_resume;
            }
            if($action == 'cartrules'){
                $this->_notice['is_running'] = false;
                if(!\LitExtension\CartMigration\Model\Cart::DEMO_MODE){
                    $this->_cart->saveRecentNotice($this->_notice);
                }
                $this->_cart->updateApi();
                $response['msg'] .= $this->_cart->consoleSuccess('Finished migration!');
            }
            $notice = $this->_cart->getNotice();
            $this->_notice['extend'] = $notice['extend'];
            $save_user = $this->_saveNotice();
            if(!$save_user){
                $response = $this->_cart->errorDatabase();
                return $this->_responseAjaxJson($response);
            }
            $response['result'] = 'no-import';
            return $this->_responseAjaxJson($response);
        }
        $total = $this->_notice[$action]['total'];
        $imported = $this->_notice[$action]['imported'];
        $error = $this->_notice[$action]['error'];
        $id_src = $this->_notice[$action]['id_src'];
        $simple_action = $this->_simple_action[$action];
        $next_action = $this->_next_action[$action];
        if($imported < $total){
            $fn_get_main = 'get' . ucfirst($action) . 'Main';
            $fn_get_ext = 'get' . ucfirst($action) . 'Ext';
            $fn_get_id = 'get' .ucfirst($simple_action) . 'Id';
            $fn_check_import = 'check' . ucfirst($simple_action) . 'Import';
            $fn_convert = 'convert' . ucfirst($simple_action);
            $fn_import = 'import' . ucfirst($simple_action);
            $fn_after_save = 'afterSave' . ucfirst($simple_action);
            $fn_addition = 'addition' . ucfirst($simple_action);

            $mains = $this->_cart->$fn_get_main();
            if($mains['result'] != 'success'){
                return $this->_responseAjaxJson($mains);
            }
            $ext = $this->_cart->$fn_get_ext($mains);
            if($ext['result'] != 'success'){
                return $this->_responseAjaxJson($ext);
            }
            foreach($mains['object'] as $main){
                if($imported >= $total){
                    break ;
                }
                $id_src = $this->_cart->$fn_get_id($main, $ext);
                $imported++;
                if($this->_cart->$fn_check_import($main, $ext)){
                    continue ;
                }
                $convert = $this->_cart->$fn_convert($main, $ext);
                if($convert['result'] == 'error'){
                    return $this->_responseAjaxJson($convert);
                }
                if($convert['result'] == 'warning'){
                    $error++;
                    $response['msg'] .= $convert['msg'];
                    continue ;
                }
                if($convert['result'] == 'pass'){
                    continue ;
                }
                if($convert['result'] == 'wait'){
                    $notice = $this->_cart->getNotice();
                    $this->_notice['extend'] = $notice['extend'];
                    $response['result'] = 'process';
                    $response[$action] = $this->_notice[$action];
                    $save_user = $this->_saveNotice();
                    if(!$save_user){
                        $response = $this->_cart->errorDatabase();
                        $this->_responseAjaxJson($response);
                        return ;
                    }
                    $this->_responseAjaxJson($response);
                    return ;
                }
                if($convert['result'] == 'addition'){
                    $data = $convert['data'];
                    $add_result = $this->_cart->$fn_addition($data, $main, $ext);
                    if($add_result['result'] != 'success'){
                        $notice = $this->_cart->getNotice();
                        $this->_notice['extend'] = $notice['extend'];
                        $response['result'] = 'process';
                        $response[$action] = $this->_notice[$action];
                        $save_user = $this->_saveNotice();
                        if(!$save_user){
                            $response = $this->_cart->errorDatabase();
                            $this->_responseAjaxJson($response);
                            return ;
                        }
                        $this->_responseAjaxJson($response);
                        return ;
                    }
                }
                $data = $convert['data'];
                $import = $this->_cart->$fn_import($data, $main, $ext);
                if($import['result'] == 'error'){
                    return $this->_responseAjaxJson($import);
                }
                if($import['result'] != 'success'){
                    $error++;
                    $response['msg'] .= $import['msg'];
                    continue ;
                }
                $mage_id = $import['mage_id'];
                $this->_cart->$fn_after_save($mage_id, $data, $main, $ext);
            }
            $response['result'] = 'process';
            $this->_notice[$action]['point'] = $this->_cart->getPoint($total, $imported);
        } else {
            $response['result'] = 'success';
            $msg_time = $this->_cart->createTimeToShow(time() - $this->_notice[$action]['time_start']);
            $response['msg'] .= $this->_cart->consoleSuccess('Finished importing ' . $action . '! Run time: ' . $msg_time);
            $response['msg'] .= $this->_cart->getMsgStartImport($next_action);
            if($next_action){
                $this->_notice[$next_action]['time_start'] = time();
            }
            $this->_notice[$action]['finish'] = true;
            $this->_notice[$action]['point'] = $this->_cart->getPoint($total, $imported, true);
            if($next_action){
                $this->_notice['fn_resume'] = 'import' . ucfirst($next_action);
            }
            if($next_action && $this->_notice['config']['import'][$next_action]){
                $fn_prepare = 'prepareImport' . ucfirst($next_action);
                $this->_cart->$fn_prepare();
            }
        }
        $this->_notice[$action]['imported'] = $imported;
        $this->_notice[$action]['id_src'] = $id_src;
        $this->_notice[$action]['error'] = $error;
        $response[$action] = $this->_notice[$action];
        $notice = $this->_cart->getNotice();
        $this->_notice['extend'] = $notice['extend'];
        if($action == 'cartrules' && $response['result'] == 'success'){
            $this->_notice['is_running'] = false;
            if(!\LitExtension\CartMigration\Model\Cart::DEMO_MODE){
                $this->_cart->saveRecentNotice($this->_notice);
            }
            $this->_cart->updateApi();
        }
        $save_user = $this->_saveNotice();
        if(!$save_user){
            $response = $this->_cart->errorDatabase();
            return $this->_responseAjaxJson($response);
        }
        return $this->_responseAjaxJson($response);
    }

    /**
     * Process after finish migration
     */
    protected function _finish(){
        $this->_initCart();
        $response = $this->_cart->finishImport();
        $this->_deleteNotice($this->_cart);
        return $this->_responseAjaxJson($response);
    }

}
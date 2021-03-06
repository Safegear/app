<?php
namespace Zenon\Megamenu\Block;

class Topmenu extends \Smartwave\Megamenu\Block\Topmenu
{

    public function getCategoryLoad($categoryId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_category = $objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
        return $_category;
    }

    public function getActiveChildCategories($category)
    {
        $children = [];
        if ($this->_categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
            $subcategories = (array)$category->getChildrenNodes();
        } else {
            $baseCategoryId = $this->_helper->getConfig('sw_megamenu/general/base_category_id'); // Get base category Id
            if($baseCategoryId != ''){
                $subcategories = $category->getChildrenCategories();
            }else{
                $subcategories = $category->getChildren();
            }
        }
        foreach($subcategories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }
            $children[] = $category;
        }
        return $children;
    }

    public function getSubmenuItemsHtml($children, $level = 1, $max_level = 0, $column_width=12, $menu_type = 'fullwidth', $columns = null)
    {
        $max_level = 3;

        $html = '';

        if(!$max_level || ($max_level && $max_level == 0) || ($max_level && $max_level > 0 && $max_level-1 >= $level)) {
            $column_class = "";
            if($level == 1 && $columns && ($menu_type == 'fullwidth' || $menu_type == 'staticwidth')) {
                $column_class = "col-sm-".$column_width." ";
                $column_class .= "mega-columns columns".$columns;
            }
            $html = '<ul class="subchildmenu '.$column_class.'">';
            foreach($children as $child) {
                $cat_model = $this->getCategoryModel($child->getId());

                $sw_menu_hide_item = $cat_model->getData('sw_menu_hide_item');

                if (!$sw_menu_hide_item) {
                    $sub_children = $this->getActiveChildCategories($child);

                    $sw_menu_cat_label = $cat_model->getData('sw_menu_cat_label');
                    $sw_menu_icon_img = $cat_model->getData('sw_menu_icon_img');
                    $sw_menu_font_icon = $cat_model->getData('sw_menu_font_icon');
                    $menu_top_content = $cat_model->getData('sw_menu_block_top_content');

                    $item_class = 'level'.$level.' ';
                    if(count($sub_children) > 0)
                        $item_class .= 'parent ';
                    $html .= '<li class="ui-menu-item '.$item_class.'">';
                    if(count($sub_children) > 0) {
                        $html .= '<div class="open-children-toggle"></div>';
                    }

                    $_childCategory = $this->getCategoryLoad($child->getId());
                    $customUrl = $_childCategory->getCustomUrl();

                    if($level == 1) {
                        if($menu_top_content) {
                            $html .= '<div class="menu-top-block">'.$this->getBlockContent($menu_top_content).'</div>';
                        }
                    }

                    if($level == 1 && $sw_menu_icon_img) {
                        //$html .= '<div class="menu-thumb-img"><a class="menu-thumb-link" href="'.$this->_categoryHelper->getCategoryUrl($child).'"><img src="' . $this->_helper->getBaseUrl().'catalog/category/' . $sw_menu_icon_img . '" alt="'.$child->getName().'"/></a></div>';

                        if($customUrl != ''):
                            $html .= '<div class="menu-thumb-img"><a class="menu-thumb-link" href="'.$customUrl.'"><img src="' . $this->_helper->getBaseUrl().'catalog/category/' . $sw_menu_icon_img . '" alt="'.$child->getName().'"/></a></div>';
                        else:
                            $html .= '<div class="menu-thumb-img"><a class="menu-thumb-link" href="'.$this->_categoryHelper->getCategoryUrl($child).'"><img src="' . $this->_helper->getBaseUrl().'catalog/category/' . $sw_menu_icon_img . '" alt="'.$child->getName().'"/></a></div>';
                        endif;

                    }
                    //$html .= '<a href="'.$this->_categoryHelper->getCategoryUrl($child).'">';

                    if($customUrl != ''):
                        $html .= '<a href="'.$customUrl.'">';
                    else:
                        $html .= '<a href="'.$this->_categoryHelper->getCategoryUrl($child).'">';
                    endif;

                    if ($level > 1 && $sw_menu_icon_img)
                        $html .= '<img class="menu-thumb-icon" src="' . $this->_helper->getBaseUrl().'catalog/category/' . $sw_menu_icon_img . '" alt="'.$child->getName().'"/>';
                    elseif($sw_menu_font_icon)
                        $html .= '<em class="menu-thumb-icon '.$sw_menu_font_icon.'"></em>';
                    $html .= '<span>'.$child->getName();
                    if($sw_menu_cat_label)
                        $html .= '<span class="cat-label cat-label-'.$sw_menu_cat_label.'">'.$this->_megamenuConfig['cat_labels'][$sw_menu_cat_label].'</span>';
                    $html .= '</span></a>';
                    if(count($sub_children) > 0) {
                        $html .= $this->getSubmenuItemsHtml($sub_children, $level+1, $max_level, $column_width, $menu_type);
                    }
                    $html .= '</li>';
                }
            }
            $html .= '</ul>';
        }

        return $html;
    }

    public function getMegamenuHtml()
    {
        $html = '';

        $baseCategoryId = $this->_helper->getConfig('sw_megamenu/general/base_category_id'); // Get base category Id
        if($baseCategoryId != ''){
            $baseCategory = $this->getCategoryModel($baseCategoryId); // Load base category
            //$categories = $baseCategory->getChildrenCategories(); // Get all children category of base category
            $categories = $this->getActiveChildCategories($baseCategory); // Get active children category of base category
        }else{
            $categories = $this->getStoreCategories(true,false,true);
        }

        $this->_megamenuConfig = $this->_helper->getConfig('sw_megamenu');

        $max_level = $this->_megamenuConfig['general']['max_level'];
        $html .= $this->getCustomBlockHtml('before');
        foreach($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }

            $cat_model = $this->getCategoryModel($category->getId());

            $sw_menu_hide_item = $cat_model->getData('sw_menu_hide_item');

            if(!$sw_menu_hide_item) {
                $children = $this->getActiveChildCategories($category);
                $sw_menu_cat_label = $cat_model->getData('sw_menu_cat_label');
                $sw_menu_icon_img = $cat_model->getData('sw_menu_icon_img');
                $sw_menu_font_icon = $cat_model->getData('sw_menu_font_icon');
                $sw_menu_cat_columns = $cat_model->getData('sw_menu_cat_columns');
                $sw_menu_float_type = $cat_model->getData('sw_menu_float_type');

                if(!$sw_menu_cat_columns){
                    $sw_menu_cat_columns = 4;
                }

                $menu_type = $cat_model->getData('sw_menu_type');
                if(!$menu_type)
                    $menu_type = $this->_megamenuConfig['general']['menu_type'];

                $custom_style = '';
                if($menu_type=="staticwidth")
                    $custom_style = ' style="width: 500px;"';

                $sw_menu_static_width = $cat_model->getData('sw_menu_static_width');
                if($menu_type=="staticwidth" && $sw_menu_static_width)
                    $custom_style = ' style="width: '.$sw_menu_static_width.';"';

                $item_class = 'level0 ';
                $item_class .= $menu_type.' ';

                $menu_top_content = $cat_model->getData('sw_menu_block_top_content');
                $menu_left_content = $cat_model->getData('sw_menu_block_left_content');
                $menu_left_width = $cat_model->getData('sw_menu_block_left_width');
                if(!$menu_left_content || !$menu_left_width)
                    $menu_left_width = 0;
                $menu_right_content = $cat_model->getData('sw_menu_block_right_content');
                $menu_right_width = $cat_model->getData('sw_menu_block_right_width');
                if(!$menu_right_content || !$menu_right_width)
                    $menu_right_width = 0;
                $menu_bottom_content = $cat_model->getData('sw_menu_block_bottom_content');
                if($sw_menu_float_type)
                    $sw_menu_float_type = 'fl-'.$sw_menu_float_type.' ';
                if(count($children) > 0 || (($menu_type=="fullwidth" || $menu_type=="staticwidth") && ($menu_top_content || $menu_left_content || $menu_right_content || $menu_bottom_content)))
                    $item_class .= 'parent ';
                $html .= '<li class="ui-menu-item '.$item_class.$sw_menu_float_type.'">';
                if(count($children) > 0) {
                    $html .= '<div class="open-children-toggle"></div>';
                }
                //$html .= '<a href="'.$this->_categoryHelper->getCategoryUrl($category).'" class="level-top">';

                $_category = $this->getCategoryLoad($category->getId());
                $customUrl = $_category->getCustomUrl();
                if($customUrl != ''):
                    $html .= '<a href="'.$customUrl.'" class="level-top">';
                else:
                    $html .= '<a href="'.$this->_categoryHelper->getCategoryUrl($category).'" class="level-top">';
                endif;

                if ($sw_menu_icon_img)
                    $html .= '<img class="menu-thumb-icon" src="' . $this->_helper->getBaseUrl().'catalog/category/' . $sw_menu_icon_img . '" alt="'.$category->getName().'"/>';
                elseif($sw_menu_font_icon)
                    $html .= '<em class="menu-thumb-icon '.$sw_menu_font_icon.'"></em>';
                $html .= '<span>'.$category->getName().'</span>';
                if($sw_menu_cat_label)
                    $html .= '<span class="cat-label cat-label-'.$sw_menu_cat_label.'">'.$this->_megamenuConfig['cat_labels'][$sw_menu_cat_label].'</span>';
                $html .= '</a>';
                if(count($children) > 0 || (($menu_type=="fullwidth" || $menu_type=="staticwidth") && ($menu_top_content || $menu_left_content || $menu_right_content || $menu_bottom_content))) {
                    $html .= '<div class="level0 submenu"'.$custom_style.'>';
                    if(($menu_type=="fullwidth" || $menu_type=="staticwidth")) {
                        $html .= '<div class="container">';
                    }
                    if(($menu_type=="fullwidth" || $menu_type=="staticwidth") && $menu_top_content) {
                        $html .= '<div class="menu-top-block">'.$this->getBlockContent($menu_top_content).'</div>';
                    }
                    if(count($children) > 0 || (($menu_type=="fullwidth" || $menu_type=="staticwidth") && ($menu_left_content || $menu_right_content))) {
                        $html .= '<div class="row">';
                        if(($menu_type=="fullwidth" || $menu_type=="staticwidth") && $menu_left_content && $menu_left_width > 0) {
                            $html .= '<div class="menu-left-block col-sm-'.$menu_left_width.'">'.$this->getBlockContent($menu_left_content).'</div>';
                        }
                        $html .= $this->getSubmenuItemsHtml($children, 1, $max_level, 12-$menu_left_width-$menu_right_width, $menu_type, $sw_menu_cat_columns);
                        if(($menu_type=="fullwidth" || $menu_type=="staticwidth") && $menu_right_content && $menu_right_width > 0) {
                            $html .= '<div class="menu-right-block col-sm-'.$menu_right_width.'">'.$this->getBlockContent($menu_right_content).'</div>';
                        }
                        $html .= '</div>';
                    }
                    if(($menu_type=="fullwidth" || $menu_type=="staticwidth") && $menu_bottom_content) {
                        $html .= '<div class="menu-bottom-block">'.$this->getBlockContent($menu_bottom_content).'</div>';
                    }
                    if(($menu_type=="fullwidth" || $menu_type=="staticwidth")) {
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                }
                $html .= '</li>';
            }
        }
        $html .= $this->getCustomBlockHtml('after');

        return $html;
    }
}

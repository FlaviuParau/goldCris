<?php

class Blugento_FullFeed_Model_System_Config_Source_Category
{

    /**
     * Options array
     *
     * @var array
     */
    public $options = null;

    /***
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $options = array();
            $collection = Mage::getResourceModel('catalog/category_collection');
            $collection->addAttributeToSelect('name')->addPathFilter('^1/[0-9/]+')->load();
            $cats = array();

            foreach ($collection as $category) {
                $cat = new stdClass();
                $cat->label = $category->getName();
                $cat->value = $category->getId();
                $cat->level = $category->getLevel();
                $cat->parentid = $category->getParentId();
                $cats[$cat->value] = $cat;
            }

            foreach ($cats as $id => $cat) {
                if (isset($cats[$cat->parentid])) {
                    if (!isset($cats[$cat->parentid]->child)) {
                        $cats[$cat->parentid]->child = array();
                    }

                    $cats[$cat->parentid]->child[] =& $cats[$id];
                }
            }

            foreach ($cats as $id => $cat) {
                if (!isset($cats[$cat->parentid])) {
                    $stack = array($cats[$id]);
                    while (count($stack) > 0) {
                        $opt = array_pop($stack);
                        $option = array(
                            'label' => ($opt->level > 1 ? str_repeat('- ', $opt->level - 1) : '') . $opt->label,
                            'value' => $opt->value
                        );
                        array_push($options, $option);
                        if (isset($opt->child) && count($opt->child)) {
                            foreach (array_reverse($opt->child) as $child) {
                                array_push($stack, $child);
                            }
                        }
                    }
                }
            }

            $this->options = $options;
        }

        return $this->options;
    }
}

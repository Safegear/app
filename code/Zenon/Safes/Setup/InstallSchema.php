<?php
namespace Zenon\Safes\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'zenon_lock'
         */

        $tableName = $installer->getTable('zenon_lock');
        $tableComment = 'Lock management for safes module';
        $columns = array(
            'entity_id' => array(
                'type' => Table::TYPE_INTEGER,
                'size' => null,
                'options' => array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
                'comment' => 'Lock Id',
            ),
            'option_id' => array(
                'type' => Table::TYPE_INTEGER,
                'size' => null,
                'options' => array('unsigned' => true, 'nullable' => false, 'primary' => true),
                'comment' => 'Lock Option Id',
            ),
            'image' => array(
                'type' => Table::TYPE_TEXT,
                'size' => 2048,
                'options' => array('nullable' => true, 'default' => ''),
                'comment' => 'Lock Image',
            ),
        );

        $indexes =  array(
            // No index for this table
        );

        $foreignKeys = array(
            'option_id' => array(
                'ref_table' => 'eav_attribute_option',
                'ref_column' => 'option_id',
                'on_delete' => Table::ACTION_CASCADE,
            )
        );

        /**
         *  We can use the parameters above to create our table
         */

        // Table creation
        //if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()->newTable($tableName);
        //}


        // Columns creation
        foreach($columns AS $name => $values){
            $table->addColumn(
                $name,
                $values['type'],
                $values['size'],
                $values['options'],
                $values['comment']
            );
        }

        // Indexes creation
        foreach($indexes AS $index){
            $table->addIndex(
                $installer->getIdxName($tableName, array($index)),
                array($index)
            );
        }

        // Foreign keys creation
        foreach($foreignKeys AS $column => $foreignKey){
            $table->addForeignKey(
                $installer->getFkName($tableName, $column, $foreignKey['ref_table'], $foreignKey['ref_column']),
                $column,
                $foreignKey['ref_table'],
                $foreignKey['ref_column'],
                $foreignKey['on_delete']
            );
        }


        // Table comment
        $table->setComment($tableComment);

        // Execute SQL to create the table
        $installer->getConnection()->createTable($table);

        // End Setup
        $installer->endSetup();
    }

}
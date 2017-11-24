<?php
namespace Zenon\Safes\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Action to do if module version is less than 1.0.1
        if(version_compare($context->getVersion(), '1.0.1') < 0) {
            /**
             * Create table 'zenon_classification'
             */
            $tableName = $installer->getTable('zenon_classification');
            $tableComment = 'Classification management for safes module';

            $columns = array(
                'entity_id' => array(
                    'type' => Table::TYPE_INTEGER,
                    'size' => null,
                    'options' => array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
                    'comment' => 'Classification Id',
                ),
                'bc_option_id' => array(
                    'type' => Table::TYPE_INTEGER,
                    'size' => null,
                    'options' => array('unsigned' => true, 'nullable' => true),
                    'comment' => 'Burglary Classification Option Id',
                ),
                'fc_option_id' => array(
                    'type' => Table::TYPE_INTEGER,
                    'size' => null,
                    'options' => array('unsigned' => true, 'nullable' => true),
                    'comment' => 'Fire Classification Option Id',
                ),
                'image' => array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 2048,
                    'options' => array('nullable' => true, 'default' => ''),
                    'comment' => 'Classification Image',
                ),
                'description' => array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 2048,
                    'options' => array('nullable' => false, 'default' => ''),
                    'comment' => 'Classification description',
                ),
                'info' => array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 2048,
                    'options' => array('nullable' => false, 'default' => ''),
                    'comment' => 'Classification information',
                ),
                'name' => array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 255,
                    'options' => array('nullable' => false, 'default' => ''),
                    'comment' => 'Classification name',
                ),
                'status' => array(
                    'type' => Table::TYPE_BOOLEAN,
                    'size' => null,
                    'options' => array('nullable' => false, 'default' => 0),
                    'comment' => 'Classification status',
                ),
            );

            $indexes =  array(
                // No index for this table
            );

            $foreignKeys = array(
                /*'bc_option_id' => array(
                    'ref_table' => 'eav_attribute_option',
                    'ref_column' => 'option_id',
                    'on_delete' => Table::ACTION_CASCADE,
                ),
                'fc_option_id' => array(
                    'ref_table' => 'eav_attribute_option',
                    'ref_column' => 'option_id',
                    'on_delete' => Table::ACTION_CASCADE,
                )*/
            );

            /**
             *  We can use the parameters above to create our table
             */

            // Table creation
            $table = $installer->getConnection()->newTable($tableName);


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
        }

        // Action to do if module version is less than 1.0.2
        if(version_compare($context->getVersion(), '1.0.2') < 0) {
            $tableName = $installer->getTable('zenon_classification');

            if ($installer->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'store_id' => [
                        'type' => Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'primary' => false,
                        'comment' => 'Store id',
                    ],
                ];

                $connection = $installer->getConnection();

                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        // Action to do if module version is less than 1.0.3
        if(version_compare($context->getVersion(), '1.0.3') < 0) {
            $tableName = $installer->getTable('zenon_classification');

            if ($installer->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'fec_option_id' => [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'unsigned' => true,
                        'primary' => false,
                        'comment' => 'Fire Extinguisher Cert id',
                    ],
                    'feca1_option_id' => [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'unsigned' => true,
                        'primary' => false,
                        'comment' => 'Fire Extinguisher Cert Add1 id',
                    ],
                    'feca2_option_id' => [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'unsigned' => true,
                        'primary' => false,
                        'comment' => 'Fire Extinguisher Cert Add2 id',
                    ],
                    'et_option_id' => [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'primary' => false,
                        'unsigned' => true,
                        'comment' => 'Eas Technology id',
                    ],
                ];

                $connection = $installer->getConnection();

                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        // End Setup
        $installer->endSetup();
    }

}
<?php

use yii\db\Migration;

/**
 * Class m190826_232724_add_date_to_comment
 */
class m190826_232724_add_date_to_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function Up()
    {
        $this->addColumn('comment', 'date', $this->date());
    }


    public function down()
    {
        $this->dropColumn('comment', 'date');
    }

}

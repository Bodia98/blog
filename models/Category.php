<?php

namespace app\models;

use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "category".
 *
 * @property int                 $id
 * @property ActiveQuery $articles
 * @property mixed               $articlesCount
 * @property string              $title
 */
class Category extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::className(), ['category_id' => 'id']);
    }

    public function getArticlesCount()
    {
        return $this->getArticles()->count();
    }

    public static function getAll()
    {
        return self::find()->all();
    }

    public static function getArticlesByCategory($id)
    {
        // build a DB query to get all articles
        $query = Article::find()->where(['category_id' => $id]);
        // get the total number of articles(but do not fetch the article data yet)
        $count = $query->count();
        // create a pagination object with the total count
        $pagination = new Pagination([
            'totalCount' => $count,
            'pageSize' => 8,
        ]);
        //limit the query using the pagination and retrieve the articles
        $articles = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $data['articles']    = $articles;
        $data['pagination']  = $pagination;

        return $data;
    }
}

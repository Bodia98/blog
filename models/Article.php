<?php

namespace app\models;

use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Yii;
use yii\data\Pagination;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "article".
 *
 * @property int          $id
 * @property string       $title
 * @property string       $description
 * @property string       $content
 * @property string       $date
 * @property string       $image
 * @property int          $viewed
 * @property int          $user_id
 * @property int          $status
 * @property int          $category_id
 *
 */
class Article extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title', 'description', 'content'], 'string'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['date'], 'default', 'value' => date('Y-m-d')],
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
            'description' => 'Description',
            'content' => 'Content',
            'date' => 'Date',
            'image' => 'Image',
            'viewed' => 'Viewed',
            'user_id' => 'User ID',
            'status' => 'Status',
            'category_id' => 'Category ID',
        ];
    }

    /**
     * @param $filename
     *
     * @return bool
     */
    public function saveImage($filename)
    {
        $this->image = $filename;

        return $this->save(false);
    }

    public function getImage()
    {
        return $this->image ? '/uploads/' . $this->image : 'no-image.png';
    }


    public function deleteImage()
    {
        $imageUploadModel = new ImageUpload();
        $imageUploadModel->deleteCurrentImage($this->image);
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        $this->deleteImage();
        return parent::beforeDelete();
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']) ;
    }

    /**
     * @param $category_id
     *
     * @return bool
     */
    public function saveCategory($category_id)
    {
        $category = Category::findOne($category_id);
        if ($category !== null)
        {
            $this->link('category', $category);
            return true;
        }
    }

    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
            ->viaTable('article_tag', ['article_id' => 'id']);
    }

    public function getSelectedTags()
    {
        $selectedIds = $this->getTags()->select('id')->asArray()->all();
        return ArrayHelper::getColumn($selectedIds, 'id');
    }


    public function saveTags($tags)
    {
        if (is_array($tags))
        {
            $this->clearCurrentTags();
            foreach($tags as $tag_id)
            {
                $tag = Tag::findOne($tag_id);
                $this->link('tags', $tag);
            }
        }
    }


    public function clearCurrentTags()
    {
        ArticleTag::deleteAll(['article_id'=>$this->id]);
    }

    public static function getAuthor()
    {
        return self::find()->where('user_id')->all();
    }

    public function getDate()
    {
        return Yii::$app->formatter->asDate($this->date);
    }

    public static function getAll($pageSize = 8)
    {
        // build a DB query to get all articles
        $query = self::find();
        // get the total number of articles(but do not fetch the article data yet)
        $count = $query->count();
        // create a pagination object with the total count
        $pagination = new Pagination([
            'totalCount' => $count,
            'pageSize' => 8,
            'pageSizeParam' => false,
            'forcePageParam' => false
        ]);
        //limit the query using the pagination and retrieve the articles
        $articles = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $data['articles']    = $articles;
        $data['pagination']  = $pagination;

        return $data;
    }

    public static function getPopular()
    {
        return self::find()->orderBy('viewed DESC')->limit(3)->all();
    }

    public static function getRecent()
    {
        return self::find()->orderBy('date DESC')->limit(4)->all();
    }

    public function saveArticle()
    {
        $this->user_id = Yii::$app->user->id;
        return $this->save();
    }
}


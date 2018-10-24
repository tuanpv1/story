<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ContentSearch represents the model behind the search form of `common\models\Content`.
 */
class ContentSearch extends Content
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'total_like', 'total_view', 'approved_at', 'created_at', 'updated_at', 'created_user_id', 'is_series', 'status', 'episode_order', 'total_episode', 'parent_id', 'rating', 'rating_count', 'total_dislike', 'total_favorite', 'type', 'honor'], 'integer'],
            [['display_name', 'ascii_name', 'image', 'author', 'short_description', 'description', 'tags', 'admin_note', 'country', 'code', 'language'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Content::find()->andWhere(['parent_id' => null]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'total_like' => $this->total_like,
            'total_view' => $this->total_view,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_user_id' => $this->created_user_id,
            'is_series' => $this->is_series,
            'status' => $this->status,
            'episode_order' => $this->episode_order,
            'total_episode' => $this->total_episode,
            'parent_id' => $this->parent_id,
            'rating' => $this->rating,
            'rating_count' => $this->rating_count,
            'total_dislike' => $this->total_dislike,
            'total_favorite' => $this->total_favorite,
            'type' => $this->type,
            'honor' => $this->honor,
        ]);

        $query->andFilterWhere(['like', 'display_name', $this->display_name])
            ->andFilterWhere(['like', 'ascii_name', $this->ascii_name])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'short_description', $this->short_description])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'tags', $this->tags])
            ->andFilterWhere(['like', 'admin_note', $this->admin_note])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'language', $this->language]);

        return $dataProvider;
    }

    public function filterEpisode($params, $created_user_id, $id)
    {
        $query = Content::find()
            ->andWhere(['parent_id' => $id])
            ->andWhere(['created_user_id' => $created_user_id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'total_like' => $this->total_like,
            'total_view' => $this->total_view,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_user_id' => $this->created_user_id,
            'is_series' => $this->is_series,
            'status' => $this->status,
            'episode_order' => $this->episode_order,
            'total_episode' => $this->total_episode,
            'parent_id' => $this->parent_id,
            'rating' => $this->rating,
            'rating_count' => $this->rating_count,
            'total_dislike' => $this->total_dislike,
            'total_favorite' => $this->total_favorite,
            'type' => $this->type,
            'honor' => $this->honor,
        ]);

        $query->andFilterWhere(['like', 'display_name', $this->display_name])
            ->andFilterWhere(['like', 'ascii_name', $this->ascii_name])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'short_description', $this->short_description])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'tags', $this->tags])
            ->andFilterWhere(['like', 'admin_note', $this->admin_note])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'language', $this->language]);

        return $dataProvider;

    }
}

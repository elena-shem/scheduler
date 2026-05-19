<?php

class Observer_Doctoralob extends Orm\Observer
{

    public function before_update(Orm\Model $model)
    {
        if($model->hours_remaining <= 0){
            $model->graduated = 1;
            $model->sendemail = 0;
            $model->active = 0;
        }else{
            $model->graduated = 0;
        }

        if($model->sendemail == 0){
            $model->active = 0;
        }
        if($model->sendemail == 1 && $model->graduated != 1){
            $model->active = 1;
        }
    }
    public function before_insert(Orm\Model $model)
    {
        if($model->hours_remaining <= 0){
            $model->graduated = 1;
            $model->sendemail = 0;
            $model->active = 0;
        }else{
            $model->graduated = 0;
            $model->sendemail = 1;
            $model->active = 1;
        }
    }
}
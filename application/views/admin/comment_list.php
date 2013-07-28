<div>
<?php foreach($comment_arr as $value):?>
<?=$value['comment_author']?>
<?=$value['comment_author_email']?>
<?=$value['comment_author_IP']?>
<?=$value['comment_content']?>
<?=$value['post_title']?>
<?=$value['comment_date']?>
<?=$value['comment_approved']?>  //审批状态
<?=$value['ID']?><br>
<?php endforeach;?>
</div>
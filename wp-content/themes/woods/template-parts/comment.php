<footer class="comment-meta">
	<div class="comment-author vcard">
		<?php echo woods_comment_author_avatar(); ?>
	</div>
	<div class="comment-metadata">
		<?php echo woods_get_comment_author_link(); ?>
		<?php echo woods_get_comment_date( array( 'format' => 'M. n' ) ); ?>
	</div>
</footer>
<div class="comment-content">
	<?php echo woods_get_comment_text(); ?>
</div>
<div class="reply">
	<?php echo woods_get_comment_reply_link( array( 'reply_text' => '<i class="material-icons">reply</i>' ) ); ?>
</div>

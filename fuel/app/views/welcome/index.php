<?php if (!empty($welcomes)): ?>

<table class="table table-hover">
    <thead>
        <tr>
            <th>Announcements:</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($welcomes as $item): ?>
            <tr>
                <td><?php echo $item->text; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
$pagination = Pagination::instance('announcements_pagination', false);
if ($pagination) echo $pagination->render();
?>

<?php else: ?>
    <p>No Announcements</p>
<?php endif; ?>

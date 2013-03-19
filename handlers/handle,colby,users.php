<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Users';
$page->descriptionHTML = 'Manage users.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

?>

<script>

"use strict";

var xhr = null;

///
///
///
function handle_response()
{
    var response = null;

    if (xhr.status != 200)
    {
        alert(xhr.status +
              ': ' +
              xhr.statusText);
    }
    else
    {
        response = JSON.parse(xhr.responseText);

        if (!response.wasSuccessful)
        {
            alert(response.message);
        }

        xhr = null;
    }

    return response;
}

///
///
///
function update_user_verification(id, hasBeenVerified)
{
    var formData = new FormData();
    formData.append('id', id);
    formData.append('hasBeenVerified', hasBeenVerified ? '1' : '0');

    xhr = new XMLHttpRequest();
    xhr.open('POST', '/colby/users/update-verification/', true);
    xhr.onload = handle_response;
    xhr.send(formData);
}

</script>

<?php
$sql = <<<END
SELECT
    USER() as `user`,
    CURRENT_USER() as `currentUser`
END;

$result = Colby::query($sql);

$row = $result->fetch_object();

$result->free();

?>

<table><thead>
<tr><th>user()</th><th>current_user()</th></tr>
</thead><tbody>
<tr><td><?php echo $row->user; ?></td><td><?php echo $row->currentUser; ?></td></tr>
</tbody></table>

<table><tbody>

    <?php

    $sql = <<<END
SELECT
    *
FROM
    `ColbyUsers`
ORDER BY
    `facebookLastName`
END;

    $result = Colby::query($sql);

    while ($row = $result->fetch_object())
    {
        ?>

        <tr>
            <td><?php echo $row->id; ?></td>
            <td><?php echo $row->facebookName; ?></td>
            <td><?php render_checkbox($row); ?></td>
        </tr>

        <?php
    }

    $result->free();

    ?>

</tbody></table>

<?php

done:

$page->end();

//
// functions
//

function render_checkbox($userRow)
{
    $checked = '';
    $disabled = '';

    if ($userRow->hasBeenVerified)
    {
        $checked = 'checked="checked"';

        // the "first verified user" can't be un-verified

        if (COLBY_FACEBOOK_FIRST_VERIFIED_USER_ID === $userRow->facebookId)
        {
            $disabled = 'disabled="disabled"';
        }
    }

    ?>

    <input type="checkbox"
           <?php echo $checked, "\n"; ?>
           <?php echo $disabled, "\n"; ?>
           onclick="update_user_verification(<?php echo $userRow->id ?>, this.checked);"> Verified

    <?php
}

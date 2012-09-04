<?php

ColbyPage::requireVerifiedUser();

$args = new stdClass();
$args->title = 'Manage Users';
$args->description = 'Use this page to manage users.';

$sql = <<< END
SELECT
    *
FROM
    `ColbyUsers`
ORDER BY
    `facebookLastName`
END;

ColbyPage::beginAdmin($args);

?>

<style>
body
{
    margin: 10px;
}

table
{
    border-collapse: collapse;
}

td
{
    border: 1px solid #dddddd;
    padding: 5px 15px;
}

tr:nth-child(even) td
{
    background-color: #eeeeee;
}
</style>

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

<h1 style="text-align: center;">Manage users</h1>

<h2>Users</h2>

<table><tbody>

    <?php

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

ColbyPage::end();

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

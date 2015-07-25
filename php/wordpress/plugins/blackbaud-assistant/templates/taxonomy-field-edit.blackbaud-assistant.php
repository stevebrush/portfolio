<tr class="form-field">
    <?php
    switch ($data['type'])
    {


        /**
         * Text.
         */
        case "text":
        default:
        ?>
        <th scope="row" valign="top">
            <label for="<?php echo $data["id"]; ?>"><?php echo $data["label"]; ?></label>
        </th>
        <td>
            <input
                type="text"
                name="<?php echo $data["name"]; ?>"
                id="<?php echo $data["id"]; ?>"
                value="<?php echo $data["value"]; ?>"
                <?php echo $data["attributes"]; ?>>
            <?php if (isset($data["helplet"])) : ?>
                <p class="description"><?php echo $data["helplet"]; ?></p>
            <?php endif; ?>
        </td>
        <?php
        break;


        /**
         * Checkbox.
         */
        case "checkbox":
            ?>
            <th scope="row" valign="top"></th>
            <td>
                <label for="<?php echo $data["id"]; ?>">
                    <input
                    type="checkbox"
                    name="<?php echo $data["name"]; ?>"
                    id="<?php echo $data["id"]; ?>"
                    <?php echo checked($data['value'], 'on', false); ?>
                    <?php echo $data["attributes"]; ?>>
                    <?php echo $data["label"]; ?>
                </label>
                <?php if (isset($data["helplet"])) : ?>
                    <p class="description"><?php echo $data["helplet"]; ?></p>
                <?php endif; ?>
            </td>
            <?php
        break;


        /**
         * Select.
         */
        case "select":
        ?>
        <th scope="row" valign="top">
            <label for="<?php echo $data["id"]; ?>"><?php echo $data["label"]; ?></label>
        </th>
        <td>
            <select name="<?php echo $data["name"]; ?>" id="<?php echo $data["id"]; ?>"<?php echo $data["attributes"]; ?>>
                <?php foreach ($data["options"] as $k => $v) : ?>
                    <option <?php echo selected($data['value'], $v, false); ?> value="<?php echo $v; ?>"><?php echo $k; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($data["helplet"])) : ?>
                <p class="description"><?php echo $data["helplet"]; ?></p>
            <?php endif; ?>
        </td>
        <?php
        break;


    }
    ?>
</tr>

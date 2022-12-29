<?php

/**
 * Show the horizontal table of exchange rate referenced to 1 unit of the reference currency.
 *
 * @link       https://viderlab.com
 * @since      1.0.0
 *
 * @package    ViderLab_Exchange_Table
 * @subpackage ViderLab_Exchange_Table/public/partials
 */
?>
<table class='viderlab-exchange-table'>
    <tr>
        <!-- Date --> 
        <?php if( isset($options['vet_field_show_date']) ): ?>
            <td class='vet-date'><?php echo date(get_option('date_format')); ?></td>
        <?php endif; ?>

        <!-- Ref currency -->
        <td class='vet-value'><?php echo '1 '.$options['vet_field_ref_currency']; ?></td>
        
        <!-- Rate values -->
        <?php 
        for( $i = 1; $i <= $options['vet_field_quantity']; $i++): 
            if( !isset($options['vet_field_rate_input_'.$i]) || !isset($options['vet_field_currency_'.$i]) ) continue;
            ?>
            <td class='vet-value'><?php echo $options['vet_field_rate_input_'.$i].' '.$options['vet_field_currency_'.$i]; ?></td>
            <?php 
        endfor;
        ?>
    </tr>
</table>

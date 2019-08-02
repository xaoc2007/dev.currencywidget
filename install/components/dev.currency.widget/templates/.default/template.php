<?php

if(is_object($arResult['VALUTE'])) {
    ?>
    <?=$arResult['VALUTE']->NOMINAL;?> <?=$arResult['VALUTE']->CHAR_CODE;?> = <?=$arResult['VALUTE']->VALUE;?> RUB<br/>
    <?
}
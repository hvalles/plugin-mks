<?php

defined('BASEPATH') OR exit('No direct script access allowed');

function paintText($field, $value, $label, $holder = '', $style='', $class='') {
    $str = "<div class='form-group $class'>";
    if ($label) $str .= "<label class='font-weight-bold' for='$field'>$label</label>";
    $str .=  "<input class='form-control' type='text' name='$field' " .
             ($holder ? "placeholder='$holder' ":'')."value='$value' ".($style ? "style='$style'":'')." >";
    $str .= '</div>';
    return $str;
}

function paintLink($field, $label, $style, $class, $default) {
    return "<div class='form-group $class'>
    <a href='$default' class='btn-primary btn btn-lg' name='$field'>$label</a>
    </div>";
}

// Always omit previous value
function paintPassword($field, $value, $label, $holder = '', $style='', $class='') {
    $str = "<div class='form-group $class'>";
    if ($label) $str .= "<label class='font-weight-bold' for='$field'>$label</label>";
    $str .=  "<input class='form-control' type='password' name='$field' " .
             ($holder ? "placeholder='$holder' ":'')."value='' ".($style ? "style='$style'":'')." >";
    $str .= '</div>';
    return $str;
}

function paintRead($field, $value, $label, $style='', $class='') {
    $str = "<div class='form-group $class'>";
    if ($label) $str .= "<label class='font-weight-bold' for='$field'>$label</label>";
    $str .=  "<input class='form-control' type='text' name='$field' readonly " .
             "value='$value' ".($style ? "style='$style'":'')." >";
    $str .= '</div>';
    return $str;
}

function paintBtn($field, $label, $style='', $class='') {
    if ($style) $style = "style='$style'";
    return "<div class='form-group'>
    <input class='form-control $class' type='button' id='id_$field' name='$field' value='$label' $style>
    </div>";
}

function paintHidden($field, $value) {
    return "<input class='form-control' type='hidden' name='$field', value='$value' >";
}

function paintArea($field, $value, $label, $holder = '', $style='', $class='', $rows=5) {
    if ($style) $style = "style='$style'";
    return "<div class='form-group $class'>
    <label class='font-weight-bold' for='$field'>$label</label>
    <textarea class='form-control' rows='$rows' name='$field' $style>$value</textarea>
    </div>";
}

function paintCheck($field, $value, $label, $style='', $class='') {
    $checked='';
    if (strlen($value)==0) $value=1;
    if ($value==1) $checked='checked';
    if ($style) $style = "style='$style'";
    return "<div class='form-check $class'>
    <input class='form-check-input' type='checkbox' name='$field' $style value='1' $checked>
    <label class='font-weight-bold' form-check-label' for='$field'>$label</label>
    </div>";
}

function paintImage($field, $value, $label, $style='', $class='', $width='') {
    if (strpos($class,'draw-only') !== FALSE && strpos(strtolower($value),'http')===FALSE) return '';
    $str = '<div class="form-group">';
    $str .= "<label  class='font-weight-bold' for='$field'>$label</label>";
    if (strpos($class,'draw-only') === FALSE) {
        $str .= "<input type='file' class='form-control-file btn btn-secondary' name='$field' id='$field' accept='.gif,.jpg,.jpeg,.png,.pdf'>";
    }
    $str .= "<div class='ml-1 mb-3 mt-3'><img src='$value' alt='imagen $field' width='$width' alt='$field' ></div>";
    $str .= '</div>';

    return $str;
}

function paintButton($name, $clase='', $value='', $href='') {
    $str = '';
    if ($name == 'btn_add') 
        $str = '<span ><input class="btn btn-secondary btn-lg"  name="btn_add" id="id_add" type="submit" value="Agregar" /></span>';        

    if ($name == 'btn_save') 
        $str = '<span ><input class="btn btn-secondary btn-lg"  name="btn_save" id="id_add" type="submit" value="Guardar" /></span>';        

    if ($name == 'btn_cancel') 
        $str = '<span class="ml-5"><input class="btn btn-warning btn-lg" name="btn_cancel" id="id_cancel" type="submit" value="Cancelar" /></span>';
    
    if ($name == 'btn_delete') 
        $str = '<span class="ml-5"><input class="btn btn-danger btn-lg" name="btn_delete" id="id_delete" type="submit" value="Eliminar" /></span>';

    if (!$str) {
        if ($href) {
            $str = "<span class='ml-5'><a class='$clase btn' name='$name' id='$name' href='$href'>$value</a></span>";
        } else {
            $str = "<span class='ml-5'><input class='$clase' name='$name' id='$name' type='submit' value='$value' /></span>";
        }
    }
        

    return $str;
}

function paintLabel($name, $value, $style, $class) {
    return "<div class='$class' id='$name' style='$style' >$value</div>";
}


function paintDropDown($field, $label, $value, $style, $inList, $related, $filter, $class, $size) {
    $str = "<div class='form-group $class'>";
    if ($label) $str .= "<label class='font-weight-bold' for='$field'>$label</label>";
    if ($size) $size = " size='$size' ";
    $str .= "<select class='form-control' name='$field' $size>";
    $arr = explode('|',$inList);
    foreach ($arr as $e) {         
        $v = $e;
        if (strpos($e,'__')!==FALSE) {
            $x = explode('__',$e);
            $v = $x[0];
            $e = $x[1];
        }
        if ($v != $value) {
            $str .= "<option value='$v'>$e</option>";
        } else {
            $str .= "<option value='$v' selected>$e</option>";
        }
    }

    $str .= '</select></div>';
    return $str;

    return $str;
}


function openGroup($class) {
    return "<div class='$class'>";
}

function paintDate($field, $label, $value, $class, $style) {

    $str = "<div class='form-group $class'>";
    if ($label) $str .= "<label class='font-weight-bold' for='$field'>$label</label>";
    $str .=  "<input class='form-control' type='date' name='$field' " .
             "value='$value' ".($style ? "style='$style'":'')." >";
    $str .= '</div>';

    return $str;
}


function paintForm($fields, $before, $class='') {
    $forma = '';

    foreach ($fields as $field=>$values):  
        $elemento = 'text';
        $label = ucfirst($field);
        $rules='';
        $inList = '';
        $relatedTo = '';
        $style='';
        $holder  = '';
        $filter  = '';
        $default = '';
        $width = '100px';
        $class='';
        $size='';
        foreach ($values as $value=>$data):
            ${$value} = $data;
        endforeach;

        if (is_string($default) && strpos($default,'field_')!==FALSE) {
            $oldvalue = $before->{str_replace('field_','',$default)} ?? '';
        } else {
            $oldvalue = $before->{$field} ?? '';
        }
        
        if ($default!='' && $oldvalue=='') $oldvalue = $default;
        
        if ($elemento == 'text') $forma .= paintText($field, $oldvalue,  $label, $holder, $style, $class);
        if ($elemento == 'button') $forma .= paintBtn($field,  $label, $style, $class);
        if ($elemento == 'read') $forma .= paintRead($field, $oldvalue,  $label, $style, $class);
        if ($elemento == 'hidden') $forma .= paintHidden($field, $oldvalue);
        if ($elemento == 'textarea') $forma .= paintArea($field, $oldvalue,  $label, $holder, $style, $class);
        if ($elemento == 'checkbox') $forma .= paintCheck($field, $oldvalue, $label, $style, $class);
        if ($elemento == 'image') $forma .= paintImage($field, $oldvalue, $label, $style, $class, $width);
        if ($elemento == 'label') $forma .= paintLabel($field, $oldvalue, $style, $class);        
        if ($elemento == 'link') $forma .= paintLink($field, $label, $style, $class, $default);
        if ($elemento == 'dropdown') $forma .= paintDropDown($field, $label, $oldvalue, $style, $inList, $relatedTo, $filter, $class, $size);
        if ($elemento == 'date') $forma .= paintDate($field, $label, $oldvalue, $class, $style);
        if ($elemento == 'password') $forma .= paintPassword($field, $oldvalue,  $label, $holder, $style, $class);

        if ($elemento == 'group') {
            $forma .= openGroup($class.' row');
            $forma .= paintForm($default, $before, 'col');
            $forma .= '</div>';
        }
    

    endforeach; 
    return $forma;
}
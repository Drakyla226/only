<?php

class CIBlockPropertyTask7
{
    private static $showedCss = false;
    private static $showedJs = false;

    public static function GetUserTypeDescription()
    {
        return array(
            //Стандартный тип поля
            "PROPERTY_TYPE"         => "S",
            //Уникальный иденкификатор типа
            "USER_TYPE"             => "MY",
            //Название типа
            "DESCRIPTION"           => "Комплекс 7 задания",
            //Вывод в ИБ
            'GetPropertyFieldHtml'  => array(__CLASS__,  'GetPropertyFieldHtml'),
            //Запись в БД
            'ConvertToDB'           => array(__CLASS__, 'ConvertToDB'),
            //Чтение с БД
            'ConvertFromDB'         => array(__CLASS__,  'ConvertFromDB'),
            //Настройки в ИБ
            'GetSettingsHTML'       => array(__CLASS__, 'GetSettingsHTML'),
            //Настройки перед сохранением
            'PrepareSettings'       => array(__CLASS__, 'PrepareUserSettings'),
            //Длина значения свойства
            'GetLength'             => array(__CLASS__, 'GetLength'),
            //Вывод на публичную часть страницы
            'GetPublicViewHTML'     => array(__CLASS__, 'GetPublicViewHTML')
        );
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $hideText = ('Свернуть');
        $clearText = ('Показать');

        self::showCss();
        self::showJs();

        if (!empty($arProperty['USER_TYPE_SETTINGS'])) :
            $arFields = self::prepareSetting($arProperty['USER_TYPE_SETTINGS']);
        else :
            return '<span>' . ('IEX_CPROP_ERROR_INCORRECT_SETTINGS') . '</span>';
        endif;

        $result = '';

        $result .= '<div class="mf-gray"><a class="cl mf-toggle">' . $hideText . '</a>';
        if ($arProperty['MULTIPLE'] === 'Y') :
            $result .= ' | <a class="cl mf-delete">' . $clearText . '</a></div>';
        endif;
        $result .= '<table class="mf-fields-list active">';

        foreach ($arFields as $code => $arItem) :
            if ($arItem['TYPE'] === 'string') :
                $result .= self::showString($code, $arItem['TITLE'], $value, $strHTMLControlName);
            elseif ($arItem['TYPE'] === 'file') :
                $result .= self::showFile($code, $arItem['TITLE'], $value, $strHTMLControlName);
            elseif ($arItem['TYPE'] === 'text') :
                $result .= self::showTextarea($code, $arItem['TITLE'], $value, $strHTMLControlName);
            elseif ($arItem['TYPE'] === 'date') :
                $result .= self::showDate($code, $arItem['TITLE'], $value, $strHTMLControlName);
            else :
                $result .= self::showHtml($code, $arItem['TITLE'], $value, $strHTMLControlName);
            endif;
        endforeach;

        $result .= '</table>';

        return $result;
    }

    public static function PrepareUserSettings($arProperty)
    {
        $result = [];
        if (!empty($arProperty['USER_TYPE_SETTINGS'])) :
            foreach ($arProperty['USER_TYPE_SETTINGS'] as $code => $value) :
                $result[$code] = $value;
            endforeach;
        endif;
        return $result;
    }

    public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        $result = '';

        if (!empty($arProperty['USER_TYPE_SETTINGS'])) :
            $arFields = self::prepareSetting($arProperty['USER_TYPE_SETTINGS']);
        endif;

        if (!empty($value['VALUE'])) :
            $result .= '<br>';

            $data = json_decode($value['VALUE'], true);
            foreach ($data as $code => $value) :
                $title = $arFields[$code]['TITLE'];
                $type = $arFields[$code]['TYPE'];

                if ($type === 'string') :
                    $result .=  $title . ': ' . $value . '<br>';
                elseif ($type === 'text') :
                    $result .=  $title . ': ' . $value . '<br>';
                elseif ($type === 'date') :
                    $result .=  $title . ': ' . $value . '<br>';
                elseif ($type === 'html_editor') :
                    $result .=  $title . ': ' . $value . '<br>';
                endif;
            endforeach;
        endif;

        return $result;
    }

    public static function ConvertToDB($arProperty, $arValue)
    {
        $arFields = self::prepareSetting($arProperty['USER_TYPE_SETTINGS']);

        foreach ($arValue['VALUE'] as $code => $value) :
            if ($arFields[$code]['TYPE'] === 'file') :
                $arValue['VALUE'][$code] = self::prepareFileToDB($value);
            endif;
        endforeach;

        $isEmpty = true;
        foreach ($arValue['VALUE'] as $v) :
            if (!empty($v)) :
                $isEmpty = false;
                break;
            endif;
        endforeach;

        if ($isEmpty === false) :
            $arResult['VALUE'] = json_encode($arValue['VALUE']);
        else :
            $arResult = ['VALUE' => '', 'DESCRIPTION' => ''];
        endif;

        return $arResult;
    }

    public static function ConvertFromDB($arProperty, $arValue)
    {
        $return = array();

        if (!empty($arValue['VALUE'])) :
            $arData = json_decode($arValue['VALUE'], true);
            foreach ($arData as $code => $value) :
                $return['VALUE'][$code] = $value;
            endforeach;
        endif;
        return $return;
    }

    private static function prepareFileToDB($arValue)
    {
        $result = false;

        if (!empty($arValue['DEL']) && $arValue['DEL'] === 'Y' && !empty($arValue['OLD'])) :
            CFile::Delete($arValue['OLD']);
        elseif (!empty($arValue['OLD'])) :
            $result = $arValue['OLD'];
        elseif (!empty($arValue['name'])) :
            $result = CFile::SaveFile($arValue, 'vote');
        elseif (!empty($arValue) && is_file($_SERVER['DOCUMENT_ROOT'] . $arValue)) :
            $arFile = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'] . $arValue);
            $result = CFile::SaveFile($arFile, 'vote');
        endif;
        return $result;
    }

    public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $btnAdd = ('Добавить');
        $settingsTitle =  ('Список полей');

        $arPropertyFields = array(
            'USER_TYPE_SETTINGS_TITLE' => $settingsTitle,
            'HIDE' => array('ROW_COUNT', 'COL_COUNT', 'DEFAULT_VALUE', 'SMART_FILTER', 'WITH_DESCRIPTION', 'FILTRABLE', 'MULTIPLE_CNT', 'IS_REQUIRED'),
            'SET' => array(
                'MULTIPLE_CNT' => 1,
                'SMART_FILTER' => 'N',
                'FILTRABLE' => 'N',
                'SEARCHABLE' => 1
            ),
        );

        self::showJsForSetting($strHTMLControlName["NAME"]);
        self::showCssForSetting();

        $result = '<tr><td colspan="2" align="center">
            <table id="many-fields-table" class="many-fields-table internal">        
                <tr valign="top" class="heading mf-setting-title">
                   <td>ID*</td>
                   <td>' . ('Название') . '</td>
                   <td>' . ('Сортировка') . '</td>
                   <td>' . ('Тип') . '</td>
                </tr>';

        $arSetting = self::prepareSetting($arProperty['USER_TYPE_SETTINGS']);

        if (!empty($arSetting)) :
            foreach ($arSetting as $code => $arItem) :
                $result .= '
                       <tr valign="top">
                           <td><input type="text" class="inp-code" size="20" value="' . $code . '"></td>
                           <td><input type="text" class="inp-title" size="35" name="' . $strHTMLControlName["NAME"] . '[' . $code . '_TITLE]" value="' . $arItem['TITLE'] . '"></td>
                           <td><input type="text" class="inp-sort" size="5" name="' . $strHTMLControlName["NAME"] . '[' . $code . '_SORT]" value="' . $arItem['SORT'] . '"></td>
                           <td>
                                <select class="inp-type" name="' . $strHTMLControlName["NAME"] . '[' . $code . '_TYPE]">
                                    ' . self::getOptionList($arItem['TYPE']) . '
                                </select>                        
                           </td>
                       </tr>';
            endforeach;
        endif;

        $result .= '
               <tr valign="top">
                    <td><input type="text" class="inp-code" size="20"></td>
                    <td><input type="text" class="inp-title" size="35"></td>
                    <td><input type="text" class="inp-sort" size="5" value="500"></td>
                    <td>
                        <select class="inp-type"> ' . self::getOptionList() . '</select>                        
                    </td>
               </tr>
             </table>   
                
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <input type="button" value="' . $btnAdd . '" onclick="addNewRows()">
                    </td>
                </tr>
                </td></tr>';

        return $result;
    }

    private static function prepareSetting($arSetting)
    {
        $arResult = [];

        foreach ($arSetting as $key => $value) :
            if (strstr($key, '_TITLE') !== false) :
                $code = str_replace('_TITLE', '', $key);
                $arResult[$code]['TITLE'] = $value;
            elseif (strstr($key, '_SORT') !== false) :
                $code = str_replace('_SORT', '', $key);
                $arResult[$code]['SORT'] = $value;
            elseif (strstr($key, '_TYPE') !== false) :
                $code = str_replace('_TYPE', '', $key);
                $arResult[$code]['TYPE'] = $value;
            endif;
        endforeach;

        if (!function_exists('cmp')) :
            function cmp($a, $b)
            {
                if ($a['SORT'] == $b['SORT']) :
                    return 0;
                endif;
                return ($a['SORT'] < $b['SORT']) ? -1 : 1;
            }
        endif;

        uasort($arResult, 'cmp');

        return $arResult;
    }

    private static function showJsForSetting($inputName)
    {
        CJSCore::Init(array("jquery"));
?>
        <script>
            function addNewRows() {
                $("#many-fields-table").append('' +
                    '<tr valign="top">' +
                    '<td><input type="text" class="inp-code" size="20"></td>' +
                    '<td><input type="text" class="inp-title" size="35"></td>' +
                    '<td><input type="text" class="inp-sort" size="5" value="500"></td>' +
                    '<td><select class="inp-type"><?= self::getOptionList() ?></select></td>' +
                    '</tr>');
            }

            $(document).on('change', '.inp-code', function() {
                var code = $(this).val();

                if (code.length <= 0) {
                    $(this).closest('tr').find('input.inp-title').removeAttr('name');
                    $(this).closest('tr').find('input.inp-sort').removeAttr('name');
                    $(this).closest('tr').find('select.inp-type').removeAttr('name');
                } else {
                    $(this).closest('tr').find('input.inp-title').attr('name', '<?= $inputName ?>[' + code + '_TITLE]');
                    $(this).closest('tr').find('input.inp-sort').attr('name', '<?= $inputName ?>[' + code + '_SORT]');
                    $(this).closest('tr').find('select.inp-type').attr('name', '<?= $inputName ?>[' + code + '_TYPE]');
                }
            });

            $(document).on('input', '.inp-sort', function() {
                var num = $(this).val();
                $(this).val(num.replace(/[^0-9]/gim, ''));
            });
        </script>
        <?
    }

    private static function getOptionList($selected = 'string')
    {
        $result = '';
        $arOption = [
            'string' => ('Строка'),
            'file' => ('Файл'),
            'text' => ('Текст'),
            'date' => ('Дата'),
            'hmtl' => ('HTML редактор'),
        ];

        foreach ($arOption as $code => $name) :
            $s = '';
            if ($code === $selected) :
                $s = 'selected';
            endif;
            $result .= '<option value="' . $code . '" ' . $s . '>' . $name . '</option>';
        endforeach;

        return $result;
    }

    private static function showCss()
    {
        if (!self::$showedCss) :
            self::$showedCss = true;
        ?>
            <style>
                .cl {
                    cursor: pointer;
                }

                .mf-gray {
                    color: #797777;
                }

                .mf-fields-list {
                    display: none;
                    padding-top: 10px;
                    margin-bottom: 10px !important;
                    margin-left: -300px !important;
                    border-bottom: 1px #e0e8ea solid !important;
                }

                .mf-fields-list.active {
                    display: block;
                }

                .mf-fields-list td {
                    padding-bottom: 5px;
                }

                .mf-fields-list td:first-child {
                    width: 300px;
                    color: #616060;
                }

                .mf-fields-list td:last-child {
                    padding-left: 5px;
                }

                .mf-fields-list input[type="text"] {
                    width: 350px !important;
                }

                .mf-fields-list textarea {
                    min-width: 350px;
                    max-width: 650px;
                    color: #000;
                }

                .mf-fields-list img {
                    max-height: 150px;
                    margin: 5px 0;
                }

                .mf-img-table {
                    background-color: #e0e8e9;
                    color: #616060;
                    width: 100%;
                }

                .mf-fields-list input[type="text"].adm-input-calendar {
                    width: 170px !important;
                }

                .mf-file-name {
                    word-break: break-word;
                    padding: 5px 5px 0 0;
                    color: #101010;
                }

                .mf-fields-list input[type="text"].mf-inp-bind-elem {
                    width: unset !important;
                }
            </style>
        <?
        endif;
    }

    private static function showJs()
    {
        $showText = ('Показать');
        $hideText = ('Свернуть');

        CJSCore::Init(array("jquery"));
        if (!self::$showedJs) :
            self::$showedJs = true;
        ?>
            <script>
                $(document).on('click', 'a.mf-toggle', function(e) {
                    e.preventDefault();

                    var table = $(this).closest('tr').find('table.mf-fields-list');
                    $(table).toggleClass('active');
                    if ($(table).hasClass('active')) {
                        $(this).text('<?= $hideText ?>');
                    } else {
                        $(this).text('<?= $showText ?>');
                    }
                });

                $(document).on('click', 'a.mf-delete', function(e) {
                    e.preventDefault();

                    var textInputs = $(this).closest('tr').find('input[type="text"]');
                    $(textInputs).each(function(i, item) {
                        $(item).val('');
                    });

                    var textarea = $(this).closest('tr').find('textarea');
                    $(textarea).each(function(i, item) {
                        $(item).text('');
                    });

                    var checkBoxInputs = $(this).closest('tr').find('input[type="checkbox"]');
                    $(checkBoxInputs).each(function(i, item) {
                        $(item).attr('checked', 'checked');
                    });

                    $(this).closest('tr').hide('slow');
                });


                // This is for multiple file type property (crutch)
                BX.ready(function() {
                    BX.addCustomEvent('onAddNewRowBeforeInner', function(data) {
                        var html_string = data.html;

                        // If cloned property of cprop
                        if ($('<div>' + html_string + '</div>').find('table.mf-fields-list').length > 0) {

                            var blocks = $(html_string).find('.adm-input-file-control.adm-input-file-top-shift');
                            if (blocks.length > 0) {

                                document.cprop_endPos = 0;
                                $(blocks).each(function(i, item) {
                                    blockId = $(item).attr('id');

                                    if (blockId !== undefined && blockId !== null && blockId.length > 0) {
                                        setTimeout(function(i, blockId, html_string) {
                                            // Remove hidden inputs
                                            var inputs = $('#' + blockId + ' .adm-input-file-new');

                                            if (inputs !== undefined && inputs.length > 0) {
                                                inputs.each(function(i, item) {
                                                    $(item).remove();
                                                })
                                            }


                                            var start_pos = html_string.indexOf("new top.BX.file_input", document.cprop_endPos);
                                            if (start_pos === -1) {
                                                start_pos = html_string.indexOf("new topWindow.BX.file_input", document.cprop_endPos);
                                            }

                                            var end_pos = html_string.indexOf(": new BX.file_input", start_pos);
                                            document.cprop_endPos = end_pos;
                                            var jsCode = html_string.substring(start_pos, end_pos);

                                            eval(jsCode);
                                        }, 500, i, blockId, html_string);
                                    }
                                });
                                document.cprop_endPos = 0;
                            }
                        }
                    });
                });
            </script>
        <?
        endif;
    }

    private static function showString($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';

        $v = !empty($arValue['VALUE'][$code]) ? htmlspecialchars($arValue['VALUE'][$code]) : '';
        $result .= '<tr>
                    <td align="right">' . $title . ': </td>
                    <td><input type="text" value="' . $v . '" name="' . $strHTMLControlName['VALUE'] . '[' . $code . ']"/></td>
                </tr>';

        return $result;
    }

    private static function showFile($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';

        if (!empty($arValue['VALUE'][$code]) && !is_array($arValue['VALUE'][$code])) :
            $fileId = $arValue['VALUE'][$code];
        elseif (!empty($arValue['VALUE'][$code]['OLD'])) :
            $fileId = $arValue['VALUE'][$code]['OLD'];
        else :
            $fileId = '';
        endif;

        if (!empty($fileId)) :
            $arPicture = CFile::GetByID($fileId)->Fetch();
            if ($arPicture) :
                $strImageStorePath = COption::GetOptionString('main', 'upload_dir', 'upload');
                $sImagePath = '/' . $strImageStorePath . '/' . $arPicture['SUBDIR'] . '/' . $arPicture['FILE_NAME'];
                $fileType = self::getExtension($sImagePath);

                if (in_array($fileType, ['png', 'jpg', 'jpeg', 'gif'])) :
                    $content = '<img src="' . $sImagePath . '">';
                else :
                    $content = '<div class="mf-file-name">' . $arPicture['FILE_NAME'] . '</div>';
                endif;

                $result = '<tr>
                        <td align="right" valign="top">' . $title . ': </td>
                        <td>
                            <table class="mf-img-table">
                                <tr>
                                    <td>' . $content . '<br>
                                        <div>
                                            <label><input name="' . $strHTMLControlName['VALUE'] . '[' . $code . '][DEL]" value="Y" type="checkbox"> ' . ("IEX_CPROP_FILE_DELETE") . '</label>
                                            <input name="' . $strHTMLControlName['VALUE'] . '[' . $code . '][OLD]" value="' . $fileId . '" type="hidden">
                                        </div>
                                    </td>
                                </tr>
                            </table>                      
                        </td>
                    </tr>';
            endif;
        else :
            $data = '';

            if ($strHTMLControlName["MODE"] === "FORM_FILL" && CModule::IncludeModule('fileman')) :
                $inputName = $strHTMLControlName['VALUE'] . '[' . $code . ']';
                $data = CFileInput::Show(
                    $inputName,
                    $fileId,
                    array(
                        "PATH" => "Y",
                        "IMAGE" => "Y",
                        "MAX_SIZE" => array(
                            "W" => COption::GetOptionString("iblock", "detail_image_size"),
                            "H" => COption::GetOptionString("iblock", "detail_image_size"),
                        ),
                    ),
                    array(
                        'upload' => true,
                        'medialib' => true,
                        'file_dialog' => true,
                        'cloud' => true,
                        'del' => false,
                        'description' => false,
                    )
                );
            endif;


            $result .= '<tr>
                    <td align="right">' . $title . ': </td>
                    <td>' . $data . '</td>
                </tr>';
        endif;

        return $result;
    }

    public static function showTextarea($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';

        $v = !empty($arValue['VALUE'][$code]) ? $arValue['VALUE'][$code] : '';
        $result .= '<tr>
                    <td align="right" valign="top">' . $title . ': </td>
                    <td><textarea rows="8" name="' . $strHTMLControlName['VALUE'] . '[' . $code . ']">' . $v . '</textarea></td>
                </tr>';

        return $result;
    }

    public static function showDate($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';

        $v = !empty($arValue['VALUE'][$code]) ? $arValue['VALUE'][$code] : '';
        $result .= '<tr>
                        <td align="right" valign="top">' . $title . ': </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="padding: 0;">
                                        <div class="adm-input-wrap adm-input-wrap-calendar">
                                            <input class="adm-input adm-input-calendar" type="text" name="' . $strHTMLControlName['VALUE'] . '[' . $code . ']" size="23" value="' . $v . '">
                                            <span class="adm-calendar-icon"
                                                  onclick="BX.calendar({node: this, field:\'' . $strHTMLControlName['VALUE'] . '[' . $code . ']\', form: \'\', bTime: true, bHideTime: false});"></span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>';

        return $result;
    }

    public static function showHtml($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';
        $v = !empty($arValue['VALUE'][$code]) ? $arValue['VALUE'][$code] : '';

        $editorId = 'editor_' . $code;
    
        $result .= '<tr>
                    <td align="right" valign="top">' . $title . ': </td>

                    <td><textarea id="' . $editorId . '" name="' . $strHTMLControlName['VALUE'] . '[' . $code . ']">' . $v . '</textarea></td>

                    <script src="https://cdn.ckeditor.com/ckeditor5/30.0.0/classic/ckeditor.js"></script>
                    <script>
                        ClassicEditor
                            .create(document.querySelector("#' . $editorId . '"))
                            .catch(error => {
                                console.error(error);
                            }
                        );
                    </script>
                    </tr>';
                    
        return $result;
    }


    private static function getExtension($filePath)
    {
        return array_pop(explode('.', $filePath));
    }

    public static function GetLength($arProperty, $arValue)
    {
        $arFields = self::prepareSetting(unserialize($arProperty['USER_TYPE_SETTINGS']));

        $result = false;
        foreach ($arValue['VALUE'] as $code => $value) :
            if ($arFields[$code]['TYPE'] === 'file') :
                if (!empty($value['name']) || (!empty($value['OLD']) && empty($value['DEL']))) :
                    $result = true;
                    break;
                endif;
            else :
                if (!empty($value)) :
                    $result = true;
                    break;
                endif;
            endif;
        endforeach;
        return $result;
    }

    private static function showCssForSetting()
    {
        if (!self::$showedCss) :
            self::$showedCss = true;
        ?>
            <style>
                .many-fields-table {
                    margin: 0 auto;
                    /*display: inline;*/
                }

                .mf-setting-title td {
                    text-align: center !important;
                    border-bottom: unset !important;
                }

                .many-fields-table td {
                    text-align: center;
                }

                .many-fields-table>input,
                .many-fields-table>select {
                    width: 90% !important;
                }

                .inp-sort {
                    text-align: center;
                }

                .inp-type {
                    min-width: 125px;
                }
            </style>
<?
        endif;
    }
}
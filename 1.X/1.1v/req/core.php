<?php

namespace DR_AWI;

trait core_var {

    protected $src, $target_path, $delOrgImg, $startingPoint, $posXYArray, $shadowColour = null;
    protected $Wpixel, $Hpixel, $width, $height, $startX, $startY, $fontFamily, $capPosX, $capPosY, $defaultMargin, $shadowGap, $topImgW, $topImgH = 0;
    protected $Allfonts = ["arial.ttf", "arizonia_reg.ttf", "opensans_light.ttf"];
    protected $img, $new_img, $img_new_name, $new_resource = null;
    protected $img_type = 1;
    protected $img_save_type = 2;
    protected $fontSize = 15;
    protected $is_resized, $have_top_image = false;
    protected $captionColour = "255,255,255";
    protected $allow_types = ["JPEG", "PNG", "WBMP", "JPG", "XBM"];
    protected $newImgWidth = 100;
    protected $newImgheight = 50;
    protected $newImgDefColour = "0,0,0";

}

trait core_cal {

    protected function borderCalc(int $bSize = 1, string $btype = "A") {
        $borderType = strtoupper(trim($btype));
        $borDet = [];

        $borderSize = $bSize * 2;
        $newWidth = $this->width + $borderSize;
        $newHeight = $this->height + $borderSize;
        $startX = $startY = $bSize;

        if ($borderType == "L") {
            $borDet["w"] = $newWidth - $bSize;
            $borDet["h"] = $newHeight - ($bSize * 2);
            $borDet["x"] = $startX;
            $borDet["y"] = 0;
        } elseif ($borderType == "R") {
            $borDet["w"] = $newWidth - $bSize;
            $borDet["h"] = $newHeight - ($bSize * 2);
            $borDet["x"] = 0;
            $borDet["y"] = 0;
        } elseif ($borderType == "B") {
            $borDet["w"] = $newWidth - ($bSize * 2);
            $borDet["h"] = $newHeight - $bSize;
            $borDet["x"] = 0;
            $borDet["y"] = 0;
        } elseif ($borderType == "T") {
            $borDet["w"] = $newWidth - ($bSize * 2);
            $borDet["h"] = $newHeight - $bSize;
            $borDet["x"] = 0;
            $borDet["y"] = $startY;
        } else {
            $borDet["w"] = $newWidth;
            $borDet["h"] = $newHeight;
            $borDet["x"] = $startX;
            $borDet["y"] = $startY;
        }
        return $borDet;
    }

    protected function agestCPos(string $pos = "M(x,y)") {
        $modifi_data1 = explode("(", $pos);
        $modifi_data2 = (count($modifi_data1) == 2) ? explode(",", $modifi_data1[1]) : [];
        $startingPos = (count($modifi_data1) == 2) ? $modifi_data1[0] : $pos;
        $tot_md2 = count($modifi_data2);
        $posModX = 0;
        $posModY = 0;
        if ($tot_md2 == 2) {
            $posModX = intval($modifi_data2[0]);
            $posModY = intval($modifi_data2[1]);
        } elseif ($tot_md2 == 1) {
            $posModX = intval($modifi_data2[0]);
        }
        $defaultMargin = $this->defaultMargin;
        if ($defaultMargin == 0) {
            $defaultMargin = $this->defaultMargin = 15;
        }
        $this->capPosX = ceil(($this->width + ($posModX) ) / 2);
        $this->capPosY = ceil(($this->height + ($posModY) ) / 2);
        if ($startingPos == "TL") {
            $this->capPosX = $defaultMargin + ($posModX);
            $this->capPosY = $defaultMargin + ($posModY);
        } elseif ($startingPos == "TM") {
            $this->capPosX = ceil(($this->width + ($posModX)) / 2);
            $this->capPosY = $defaultMargin + ($posModY);
        } elseif ($startingPos == "BL") {
            $this->capPosX = $defaultMargin + ($posModX);
            $this->capPosY = (($this->height + ($posModY)) - $defaultMargin);
        } elseif ($startingPos == "BM") {
            $this->capPosX = ceil(($this->width + ($posModX)) / 2);
            $this->capPosY = (($this->height + ($posModY)) - $defaultMargin);
        } elseif ($startingPos == "ML") {
            $this->capPosX = $defaultMargin + ($posModX);
            $this->capPosY = ceil(($this->height + ($posModY)) / 2);
        } else {
            $xyPos = explode(",", $startingPos);
            if (count($xyPos) == 2) {
                $this->capPosX = abs(intval($xyPos[0]));
                $this->capPosY = abs(intval($xyPos[1]));
            }
        }
    }

}

trait core_FC {

    protected function getFont(int $fontNo = 0) {
        $Allfonts = $this->Allfonts;
        if ($fontNo < count($Allfonts)) {
            return $Allfonts[$fontNo];
        } else {
            return $Allfonts[0];
        }
    }

    protected function getRebValues(string $colorPattern) {
        $colours = explode(",", $colorPattern);
        $col_arr = [];
        if (count($colours) == 3) {
            $col_arr["r"] = intval($colours[0]);
            $col_arr["g"] = intval($colours[1]);
            $col_arr["b"] = intval($colours[2]);
        } else {
            $col_arr["r"] = 255;
            $col_arr["g"] = 255;
            $col_arr["b"] = 255;
        }
        return $col_arr;
    }

}

trait core_img {

    protected function create_new_img() {
        $base_img = $this->img;
        if ($base_img == null) {
            $base_img = $this->new_img;
        }
        $new_img = FALSE;
        if (($this->Wpixel > $this->width) && ($this->Hpixel < $this->height)) {
            $new_img = $this->new_img = imagecrop($base_img, ['x' => $this->startX, 'y' => $this->startY, 'width' => $this->width, 'height' => $this->Hpixel]);
        } elseif (($this->Wpixel < $this->width) && ($this->Hpixel > $this->height)) {
            $new_img = $this->new_img = imagecrop($base_img, ['x' => $this->startX, 'y' => $this->startY, 'width' => $this->Wpixel, 'height' => $this->height]);
        } elseif (($this->Wpixel > $this->width) && ($this->Hpixel > $this->height)) {
            $new_img = $this->new_img = imagecrop($base_img, ['x' => $this->startX, 'y' => $this->startY, 'width' => $this->width, 'height' => $this->height]);
        } else {
            $new_img = $this->new_img = imagecrop($base_img, ['x' => $this->startX, 'y' => $this->startY, 'width' => $this->Wpixel, 'height' => $this->Hpixel]);
        }
        return $new_img;
    }

    function getImage64BaseString(string $returnImg = "PNG") {
        $imgType = strtoupper(trim($returnImg));
        ob_start();
        if ($imgType == "JPEG" || $imgType == "JPG") {
            imagejpeg($this->new_img);
        } elseif ($imgType == "WBMP") {
            imagewbmp($this->new_img);
        } elseif ($imgType == "GIF") {
            imagegif($this->new_img);
        } elseif ($imgType == "XBM") {
            imagexbm($this->new_img);
        } else {
            imagepng($this->new_img);
        }
        return base64_encode(ob_get_clean());
    }

}

trait core_path {

    protected function creatBasePath(string $target_path) {
        $full_path = $_SERVER['PHP_SELF'];
        $can_rev_paths = explode("/", $full_path);
        $allow_rev = count($can_rev_paths) - 2;
        $path_rev = explode("../", $target_path);
        $fparts = count($path_rev);
        if ($fparts > 0) {
            $path_str = "";
            $tot_backs = 0;
            while ($fparts > 0) {
                if ($path_rev[$fparts - 1] == "") {
                    $path_str .= "../";
                    $tot_backs++;
                }
                $fparts--;
            }
            if ($tot_backs > $allow_rev) {
                die($tot_backs . " _ " . $allow_rev . " _ " . $full_path);
            }
            $path_for = explode("/", $path_rev[$tot_backs]);
            $main = 0;
            while ($main < count($path_for)) {
                if ($path_for[$main] != "") {
                    $path_str .= $path_for[$main] . "/";
                    if (!is_dir($path_str)) {
                        if (!mkdir($path_str)) {
                            return false;
                        }
                    }
                }
                $main++;
            }
        } else {
            $path_for = explode("/", $path_rev[$tot_backs + 1]);
            $main = 0;
            while ($main < count($path_for)) {
                if ($path_for[$main] != "") {
                    $path_str .= $path_for[$main] . "/";
                    if (!is_dir($path_str)) {
                        if (!mkdir($path_str)) {
                            return false;
                        }
                    }
                }
                $main++;
            }
        }
        return (is_dir($target_path)) ? true : false;
    }

}

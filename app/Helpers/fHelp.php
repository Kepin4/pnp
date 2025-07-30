<?php
function xTest()
{
    return "Kiw";
}
function format_rupiah($angka)
{
    return "Rp " . number_format($angka, 2, ',', '.');
}

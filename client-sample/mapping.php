<?php

# mapping field dikti dg field di unesa
# contoh: di bawah adl field yg mandatory

$map_mhs = array();
$map_mhs['nm_pd'] = 'nama';
$map_mhs['regpd_nipd'] = 'npm';
$map_mhs['tgl_lahir'] = 'tgllahir';
$map_mhs['tmpt_lahir'] = 'tmplahir';
$map_mhs['jln'] = 'alamat';
$map_mhs['jk'] = 'sex';
$map_mhs['kode_pos'] = 'kodepos';
$map_mhs['telepon_rumah'] = 'telp';
$map_mhs['telepon_seluler'] = 'hp';
$map_mhs['email'] = 'email';
$map_mhs['nm_ayah'] = 'namaayah';
$map_mhs['nm_ibu_kandung'] = 'namaibu';
$map_mhs['kode_pos'] = 'kodepos';
$map_mhs['stat_pd'] = 'statusmhs';


$map_dosen = array();
$map_dosen['nm_ptk'] = 'nama';
$map_dosen['jk'] = 'sex';
$map_dosen['tmpt_lahir'] = 'tmplahir';
$map_dosen['tgl_lahir'] = 'tgllahir';
$map_dosen['nip'] = 'nip';
$map_dosen['jln'] = 'alamat';
$map_dosen['id_agama'] = 'kodeagama';
$map_dosen['nm_ibu_kandung'] = 'namaibu';
$map_dosen['stat_kawin'] = 'statusnikah';

$map_matkul = array();
$map_matkul['kode_mk'] = 'kodemk';
$map_matkul['nm_mk'] = 'namamk';
$map_matkul['sks_mk'] = 'sks';

$map_kurikulumsp = array();
$map_kurikulumsp['id_smt_berlaku'] = 'tahun';

$map_matkul_kurikulum = array();
$map_matkul_kurikulum['kode_mk'] = 'kodemk';
$map_matkul_kurikulum['nm_mk'] = 'namamk';
$map_matkul_kurikulum['smt'] = 'semmk';
$map_matkul_kurikulum['sks_mk'] = 'sks';
$map_matkul_kurikulum['a_wajib'] = 'wajibpilihan';


$map_kelas_kuliah = array();
$map_kelas_kuliah['id_smt'] = 'periode';
$map_kelas_kuliah['kode_mk'] = 'kodemk';
$map_kelas_kuliah['nm_mk'] = 'namamk';
$map_kelas_kuliah['nm_kls'] = 'kelasmk';
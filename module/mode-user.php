<?php

function insert($data) {
    global $koneksi;

    $username   = strtolower(mysqli_real_escape_string($koneksi, $data['username']));
    $fullname   = mysqli_real_escape_string($koneksi, $data['fullname']);
    $password   = mysqli_real_escape_string($koneksi, $data['password']);
    $password2  = mysqli_real_escape_string($koneksi, $data['password2']);
    $level      = mysqli_real_escape_string($koneksi, $data['level']);
    $address    = mysqli_real_escape_string($koneksi, $data['address']);

    // Cek konfirmasi password
    if ($password !== $password2) {
        echo "<script>
            alert('Konfirmasi password tidak sesuai, user baru gagal diregistrasi!');
        </script>";
        return false;
    }

    // Cek username duplikat
    $cekUsername = mysqli_query($koneksi, "SELECT username FROM tbl_user WHERE username = '$username'");
    if (mysqli_num_rows($cekUsername) > 0) {
        echo "<script>
            alert('Username sudah terpakai, user baru gagal diregistrasi!');
        </script>";
        return false;
    }

    // Hash password
    $pass = password_hash($password, PASSWORD_DEFAULT);

    // Upload gambar jika ada
    if ($_FILES['image']['name'] != '') {
        $gambar = uploadimg();
        if (!$gambar) {
            // Jika upload gagal
            return false;
        }
    } else {
        $gambar = 'default.png';
    }

    // Simpan ke database
    $sqlUser = "INSERT INTO tbl_user VALUES (null, '$username', '$fullname', '$pass', '$address', '$level', '$gambar')";
    if (!mysqli_query($koneksi, $sqlUser)) {
        echo "<script>alert('Terjadi kesalahan saat menyimpan ke database: " . mysqli_error($koneksi) . "');</script>";
        return false;
    }

    return mysqli_affected_rows($koneksi);
}

function delete($id, $foto) {
    global $koneksi;

    $sqlDel = "DELETE FROM tbl_user WHERE userid = $id";
    mysqli_query($koneksi, $sqlDel);
    if($foto != 'default.png') {
        unlink('../asset/image/' . $foto);
    }

    return mysqli_affected_rows($koneksi);
}


?>
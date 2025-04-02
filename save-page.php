('Content-Type: application/json');

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => '只接受POST请求']);
    exit;
}

// 获取操作类型
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'save_page') {
    // 保存生成的HTML页面
    $filename = isset($_POST['filename']) ? $_POST['filename'] : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    
    // 验证文件名格式 (6位字母数字)
    if (!preg_match('/^[a-zA-Z0-9]{6}\.html$/', $filename)) {
        echo json_encode(['success' => false, 'message' => '无效的文件名格式']);
        exit;
    }
    
    // 确保内容不为空
    if (empty($content)) {
        echo json_encode(['success' => false, 'message' => '页面内容不能为空']);
        exit;
    }
    
    // 保存文件
    $filepath = __DIR__ . '/pages/' . $filename;
    if (file_put_contents($filepath, $content)) {
        echo json_encode(['success' => true, 'message' => '页面保存成功', 'path' => 'pages/' . $filename]);
    } else {
        echo json_encode(['success' => false, 'message' => '保存页面失败']);
    }
} elseif ($action === 'upload_svga') {
    // 处理SVGA文件上传
    if (!isset($_FILES['svga_file'])) {
        echo json_encode(['success' => false, 'message' => '没有找到上传的文件']);
        exit;
    }
    
    $file = $_FILES['svga_file'];
    $filename = isset($_POST['filename']) ? $_POST['filename'] : '';
    
    // 验证文件名格式 (6位字母数字)
    if (!preg_match('/^[a-zA-Z0-9]{6}\.svga$/', $filename)) {
        echo json_encode(['success' => false, 'message' => '无效的文件名格式']);
        exit;
    }
    
    // 检查文件类型
    $fileType = mime_content_type($file['tmp_name']);
    if ($fileType !== 'application/octet-stream' && $fileType !== 'application/zip') {
        echo json_encode(['success' => false, 'message' => '不支持的文件类型']);
        exit;
    }
    
    // 保存文件
    $uploadPath = __DIR__ . '/uploads/' . $filename;
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        echo json_encode(['success' => true, 'message' => '文件上传成功', 'path' => 'uploads/' . $filename]);
    } else {
        echo json_encode(['success' => false, 'message' => '文件上传失败']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '未知操作']);
}
?>
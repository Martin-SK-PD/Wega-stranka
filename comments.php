<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'comments';
try {
    $pdo = new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    
    exit('Failed to connect to database: ' . $exception->getMessage());
}



function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $w = floor($diff->d / 7);
    $diff->d -= $w * 7;

    $string = [
        'y' => $diff->y > 4 ? 'rokov' : ($diff->y > 1 ? 'roky' : 'rok'),
        'm' => $diff->m > 4 ? 'mesiacov' : ($diff->m > 1 ? 'mesiace' : 'mesiac'),
        'w' => $w > 4 ? 'týždnov' : ($w > 1 ? 'týždne' : 'týždeň'),
        'd' => $diff->d > 1 ? 'dni' : 'deň',
        'h' => $diff->h > 4 ? 'hodín' : ($diff->h > 1 ? 'hodini' : 'hodinu'),
        'i' => $diff->i > 4 ? 'minút' : ($diff->i > 1 ? 'minúty' : 'minutu'),
        's' => $diff->s > 4 ? 'sekúnd' : ($diff->s > 1 ? 'sekundy' : 'sekundu')
    ];

    foreach ($string as $k => &$v) {
        if ($k == 'w' && $w) {
            $v = $w . ' ' . $v;
        } else if ($k == 'y' && isset($diff->$k) && $diff->$k) {
            $v = $diff->$k . ' ' . $v;
        } else if ($k == 'm' && isset($diff->$k) && $diff->$k) {
            $v = $diff->$k . ' ' . $v;
        } else if (isset($diff->$k) && $diff->$k) {
            $v = $diff->$k . ' ' . $v;
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' dozadu' : 'práve teraz';
}




function show_comments($comments, $parent_id = -1) {
    $html = '';
    if ($parent_id != -1) {
        
        array_multisort(array_column($comments, 'submit_date'), SORT_ASC, $comments);
    }
    
    foreach ($comments as $comment) {
        if ($comment['parent_id'] == $parent_id) {
            
            $html .= '
            <div class="comment">
                <div>
                    <h3 class="name">' . htmlspecialchars($comment['name'], ENT_QUOTES) . '</h3>
                    <span class="date">' . time_elapsed_string($comment['submit_date']) . '</span>
                </div>
                <p class="content">' . nl2br(htmlspecialchars($comment['content'], ENT_QUOTES)) . '</p>
                <a class="reply_comment_btn" href="#" data-comment-id="' . $comment['id'] . '">Odpovedať</a>
                ' . show_write_comment_form($comment['id']) . '
                <div class="replies">
                ' . show_comments($comments, $comment['id']) . '
                </div>
            </div>
            ';
        }
    }


    return $html;
}


function show_write_comment_form($parent_id = -1) {
    $html = '
    <div class="write_comment" data-comment-id="' . $parent_id . '">
        <form style="background-color: rgba(255,255,255,0)">
            <input name="parent_id" type="hidden" value="' . $parent_id . '">
            <input name="name" type="text" placeholder="Vaše meno" required>
            <textarea name="content" placeholder="Tu napíšte Váš komentár..." required></textarea>
            <button type="submit">Odoslať</button>
        </form>
    </div>
    ';
    return $html;
}


if (isset($_GET['page_id'])) {
    
    if (isset($_POST['name'], $_POST['content'])) {
       
        $stmt = $pdo->prepare('INSERT INTO comments (page_id, parent_id, name, content, submit_date) VALUES (?,?,?,?,NOW())');
        $stmt->execute([ $_GET['page_id'], $_POST['parent_id'], $_POST['name'], $_POST['content'] ]);
        
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }
    
    $stmt = $pdo->prepare('SELECT * FROM comments WHERE page_id = ? ORDER BY submit_date DESC');
    $stmt->execute([ $_GET['page_id'] ]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('SELECT COUNT(*) AS total_comments FROM comments WHERE page_id = ?');
    $stmt->execute([ $_GET['page_id'] ]);
    $comments_info = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    exit('No page ID specified!');
}
?>

<div class="comment_header">
    <span class="total"><?=$comments_info['total_comments']?> komentárov</span>
    <a href="#" class="write_comment_btn" data-comment-id="-1">Napísať komentár</a>
</div>

<?=show_write_comment_form()?>
<?=show_comments($comments)?>

<?php

class ThreadController extends AppController
{
    public function index()
    {
        $user = new User;
        $title = 'Threads';
        $page = Param::get('page', 1);
        $per_page = 5;
        $pagination = new SimplePagination($page, $per_page);
        $threads = Thread::getAll($pagination->start_index - 1, $pagination->count + 1);
        $pagination->checkLastPage($threads);
        $total = Thread::countAll();
        $pages = ceil($total / $per_page);
        $this->set(get_defined_vars());
    }
    public function view()
    {
        $user = new User;
        $thread = Thread::get(Param::get('thread_id'));
        $thread_id = Param::get('thread_id');
        $thread_info = $thread->getThreadInfo($thread_id);

        $comment = new Comment;
        $comment_page = Param::get('comment_page',1);
        $per_page = 5;
        $pagination = new SimplePagination($comment_page, $per_page);
        $comments = Comment::getByThreadId($thread_id, $pagination->start_index - 1, $pagination->count + 1);
        $pagination->checkLastPage($comments);
        $total = Comment::countAllComments($thread_id);
        $pages = ceil($total / $per_page);

        $this->set(get_defined_vars());
    }
    public function write()
    {
        $user = new User;
        $thread = Thread::get(Param::get('thread_id'));
        $comment = new Comment;
        $page = Param::get('page_next','write');
        switch ($page) {
            case 'write':
                break;
            case 'write_end':
                $comment->user_id = User::getUserId($_SESSION['username']);
                $comment->body = Param::get('body');
                try {
                    $comment->write($thread);
                } catch (ValidationException $e) {
                    $page = 'write';
                }
                break;
            default:
                throw new NotFoundException("{$page} is not found");
                break;
        }
        $this->set(get_defined_vars());
        $this->render($page);
    }
    public function create()
    {
        $user = new User;
        $thread = new Thread;
        $comment = new Comment;
        $page = Param::get('page_next', 'create');
        switch ($page) {
            case 'create':
                break;
            case 'create_end':
                $thread->title = Param::get('title');
                $comment->user_id = User::getUserId($_SESSION['username']);
                $comment->body = Param::get('body');
                try {
                    $thread->create($comment);
                } catch (ValidationException $e) {
                    $page = 'create';
                }
                break;
            default:
                throw new NotFoundException("{$page} is not found");
            break;
        }
        $this->set(get_defined_vars());
        $this->render($page);
    }
}

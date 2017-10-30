<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=performance', 'root','rootroot');

// choose all -> throw exception when article does not have a category

$articles = loadArticlesFromDb($pdo);

//choose only one

//$articleOne = loadArticleFromDb($pdo, 3);

function loadArticlesFromDb($pdo){
    $query = "select id, title, content from articles";
    $resultArticles = $pdo->query($query)->fetchAll();

    $articles = [];

    $i = 0;
    foreach($resultArticles as $article){
        $categoriesQuery = "select categories.id, categories.category_title FROM articles_categories INNER JOIN categories ON category_id = categories.id WHERE articles_id = ".$article["id"];
        $resultQuery = $pdo->query($categoriesQuery)->fetchAll();
        if(empty($resultQuery)){
            throw new Exception("Article ".$article["id"]." no have a category");
        }
        $articles[$i] = new Article($article["id"]);
        $articles[$i]->setTitle($article["title"]);
        $articles[$i]->setContent($article["content"]);
        $articles[$i]->setCategories(createCategoriesFromDb($resultQuery));
        $i++;
    }
    return $articles;
}

/**
 * @param $pdo
 * @param $articleId
 * @return Article|null
 */
function loadArticleFromDb($pdo, $articleId){
    $query = "select id, title, content from articles where id=?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$articleId]);

    $resultArticle = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(empty($resultArticle)){
        return NULL;
    }

    $categoriesQuery = "select categories.id, categories.category_title FROM articles_categories INNER JOIN categories ON category_id = categories.id WHERE articles_id = ".$resultArticle[0]["id"];
    $resultQuery = $pdo->query($categoriesQuery)->fetchAll();

    $article = new Article($resultArticle["id"]);
    $article->setTitle($resultArticle["title"]);
    $article->setContent($resultArticle["content"]);
    $article->setCategories(createCategoriesFromDb($resultQuery));

    return $article;
}

function createCategoriesFromDb($resultQuery){
    $categories = [];

    $i = 0;
    foreach($resultQuery as $category){
        $categories[$i] = new Category($category["id"]);
        $categories[$i]->setCategoryTitle($category["category_title"]);
        $i++;
    }
    return $categories;
}

class Article {

    private $id;
    private $title;
    private $content;
    private $categories;

    public function __construct($id){
        $this->id = $id;
    }
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }


    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

}

class Category {
    private $id;
    private $categoryTitle;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCategoryTitle()
    {
        return $this->categoryTitle;
    }

    /**
     * @param mixed $categoryTitle
     */
    public function setCategoryTitle($categoryTitle)
    {
        $this->categoryTitle = $categoryTitle;
    }



}
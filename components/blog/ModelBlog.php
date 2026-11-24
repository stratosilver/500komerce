<?php
/**
 * Blog: the articles of the table post, as the visitors read them.
 * Only the published articles are listed: status "published", not deleted, publication date reached.
 * The articles are those of the language of the page (LANG); the back office manages them (component post).
 * The texts are stored html-escaped by the forms, like in the other tables.
 */

declare(strict_types=1);
namespace Apgenic\Blog;

class ModelBlog extends \Apgenic\Classes\Model{

    /** Articles by page of the list */
    const PER_PAGE = 9;

    /** Columns read for an article: the article, its image and its author */
    const SELECT = "SELECT p.`id_post`, p.`lang`, p.`title`, p.`slug`, p.`excerpt`, p.`body`, p.`status`,
                           COALESCE(p.`published_at`, p.`created_at`) AS `date`,
                           m.`filename`, m.`alt_text`, u.`first_name`, u.`last_name`
                    FROM `post` p
                    LEFT JOIN `media` m ON m.`id_media` = p.`id_media` AND m.`deleted_at` IS NULL
                    LEFT JOIN `user` u ON u.`id_user` = p.`id_user`";

    /** Condition of a published article */
    const PUBLISHED = "p.`status` = 'published' AND p.`deleted_at` IS NULL AND (p.`published_at` IS NULL OR p.`published_at` <= datetime('now', 'localtime'))";


    /**
     * Number of published articles in a language
     * @return int
     */
    public function count(string $lang):int{
        $q = self::$db->prepare("SELECT COUNT(*) FROM `post` p WHERE ".self::PUBLISHED." AND p.`lang` = :lang");
        $q->execute(array(':lang' => $lang));
        return (int)$q->fetchColumn();
    }


    /**
     * Published articles of a language, the most recent first
     * @param int $page from 1
     * @return array rows
     */
    public function getList(string $lang, int $page = 1):array{
        $offset = (max(1, $page) - 1) * self::PER_PAGE;
        $q = self::$db->prepare(self::SELECT." WHERE ".self::PUBLISHED." AND p.`lang` = :lang
                                 ORDER BY `date` DESC, p.`id_post` DESC
                                 LIMIT ".self::PER_PAGE." OFFSET ".$offset);
        $q->execute(array(':lang' => $lang));
        return $q->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * One article, by its slug: the one of the language of the page first, else the one of another language.
     * @param bool $withUnpublished also the drafts and the archived articles (preview of the editors)
     * @return array|null null when there is no such article
     */
    public function getBySlug(string $slug, string $lang, bool $withUnpublished = false):?array{
        $where = $withUnpublished ? "p.`deleted_at` IS NULL" : self::PUBLISHED;
        $q = self::$db->prepare(self::SELECT." WHERE ".$where." AND p.`slug` = :slug
                                 ORDER BY p.`lang` = :lang DESC LIMIT 1");
        $q->execute(array(':slug' => $slug, ':lang' => $lang));
        $row = $q->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    /**
     * One article, by its id (old links, back office)
     * @return array|null
     */
    public function getById(int $idPost, bool $withUnpublished = false):?array{
        $where = $withUnpublished ? "p.`deleted_at` IS NULL" : self::PUBLISHED;
        $q = self::$db->prepare(self::SELECT." WHERE ".$where." AND p.`id_post` = :id");
        $q->execute(array(':id' => $idPost));
        $row = $q->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    /**
     * Other published articles of the same language, displayed under an article
     * @return array rows
     */
    public function getOthers(string $lang, int $exceptIdPost, int $limit = 3):array{
        $q = self::$db->prepare(self::SELECT." WHERE ".self::PUBLISHED." AND p.`lang` = :lang AND p.`id_post` <> :id
                                 ORDER BY `date` DESC, p.`id_post` DESC
                                 LIMIT ".max(1, $limit));
        $q->execute(array(':lang' => $lang, ':id' => $exceptIdPost));
        return $q->fetchAll(\PDO::FETCH_ASSOC);
    }
}

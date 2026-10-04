<?php
namespace Apgenic\Classes;

trait ViewTrait{

    function displayDate($dateMysqlFormat){
        if($dateMysqlFormat != null) {
            echo substr($dateMysqlFormat, 8, 2) . '/' . substr($dateMysqlFormat, 5, 2) . '/' . substr($dateMysqlFormat, 0, 4);
        }
    }

    function getDate($dateMysqlFormat){
        if($dateMysqlFormat != null) {
            return(substr($dateMysqlFormat, 8, 2) . '/' . substr($dateMysqlFormat, 5, 2) . '/' . substr($dateMysqlFormat, 0, 4));
        }
    }

    function displayDateTime($dateTimeMysqlFormat){
        if($dateTimeMysqlFormat != null) {
            echo substr($dateTimeMysqlFormat, 8, 2) . '/' . substr($dateTimeMysqlFormat, 5, 2) . '/' . substr($dateTimeMysqlFormat, 0, 4);
            echo ' ' . substr($dateTimeMysqlFormat, 11);
        }
    }

    function newPagination(){
        ?>
        <style>
            .pagination.flex-nowrap { display: flex; flex-wrap: nowrap; }
            .pagination .page-first { margin-right: auto; }
            .pagination .page-last  { margin-left: auto; }
        </style>
        <nav aria-label="Pagination">
            <ul class="pagination flex-nowrap align-items-center w-100">

                <!-- Première page : à gauche -->
                <li class="page-item active page-first">
                    <a class="page-link" href="?component=personnel&amp;task=editlist&amp;&amp;orderBy=&amp;order=asc&amp;page=1">1</a>
                </li>

                <!-- Bloc central : courante-1, courante, courante+1 -->
                <li class="page-item disabled"><span class="page-link">…</span></li>
                <li class="page-item">
                    <a class="page-link" href="?component=personnel&amp;task=editlist&amp;&amp;orderBy=&amp;order=asc&amp;page=2">2</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="?component=personnel&amp;task=editlist&amp;&amp;orderBy=&amp;order=asc&amp;page=3">3</a>
                </li>
                <li class="page-item disabled"><span class="page-link">…</span></li>

                <!-- Dernière page : à droite -->
                <li class="page-item page-last">
                    <a class="page-link" href="?component=personnel&amp;task=editlist&amp;&amp;orderBy=&amp;order=asc&amp;page=142">142</a>
                </li>

            </ul>
        </nav>
        <?php
    }

}

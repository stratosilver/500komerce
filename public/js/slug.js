/**
 * Slug filled while the name is typed (forms of the products and of the articles of the back office).
 *
 *   <input id="name" data-slug-target="slug">     the name, or the title
 *   <input id="slug" data-slug-auto="1">           its slug: "1" follows the name, "0" typed by hand
 *
 * The slug follows the name while it is empty, or for a new entry while it is the slug of the name. Typed by
 * hand, it stops following the name; emptied, it follows it again. When the field is left, what is typed is
 * cleaned the same way (letters without accents, digits, -).
 * komerceSlug() gives the same result as \Apgenic\Classes\Url::slug() (classes/Url.php), which makes the slug
 * when the browser has not: keep both the same.
 * The forms are loaded by htmx: the events are listened on the document, the fields are found when they happen.
 */
(function(){
    'use strict';

    // Letters of Url::slug()
    var LETTERS = {
        'à': 'a', 'á': 'a', 'â': 'a', 'ã': 'a', 'ä': 'a', 'å': 'a', 'ā': 'a', 'æ': 'ae',
        'ç': 'c', 'č': 'c', 'ć': 'c', 'đ': 'd', 'ď': 'd',
        'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e', 'ē': 'e', 'ę': 'e', 'ě': 'e',
        'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i', 'ī': 'i', 'ł': 'l', 'ľ': 'l',
        'ñ': 'n', 'ń': 'n', 'ň': 'n', 'ò': 'o', 'ó': 'o', 'ô': 'o', 'õ': 'o', 'ö': 'o', 'ø': 'o', 'ō': 'o', 'œ': 'oe',
        'ř': 'r', 'ś': 's', 'š': 's', 'ß': 'ss', 'ť': 't', 'ù': 'u', 'ú': 'u', 'û': 'u', 'ü': 'u', 'ū': 'u', 'ů': 'u',
        'ý': 'y', 'ÿ': 'y', 'ź': 'z', 'ż': 'z', 'ž': 'z',
        'ă': 'a', 'ą': 'a', 'ĉ': 'c', 'ĝ': 'g', 'ğ': 'g', 'ĥ': 'h', 'ı': 'i', 'ĵ': 'j',
        'ő': 'o', 'ŝ': 's', 'ş': 's', 'ș': 's', 'ț': 't', 'ţ': 't', 'ŭ': 'u', 'ű': 'u'
    };

    function komerceSlug(text, max){
        max = max || 200;
        var slug = String(text || '').toLowerCase().replace(/[^\u0000-\u007f]/g, function(c){
            return LETTERS.hasOwnProperty(c) ? LETTERS[c] : c;
        });
        if(slug.normalize){
            slug = slug.normalize('NFD');
        }
        // Accents written apart from their letter
        slug = slug.replace(/[\u0300-\u036f]+/g, '');
        slug = slug.replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        return slug.slice(0, max).replace(/-+$/, '');
    }
    window.komerceSlug = komerceSlug;

    // Typing the name, or the slug
    document.addEventListener('input', function(event){
        var field = event.target;
        if(!field || !field.dataset){
            return;
        }
        if(field.dataset.slugTarget){
            var slug = document.getElementById(field.dataset.slugTarget);
            if(slug && slug.dataset.slugAuto === '1'){
                slug.value = komerceSlug(field.value);
            }
        }
        else if(field.dataset.slugAuto !== undefined){
            field.dataset.slugAuto = field.value === '' ? '1' : '0';
        }
    });

    // Leaving the slug: cleaned; emptied, it is the slug of the name again
    document.addEventListener('change', function(event){
        var field = event.target;
        if(!field || !field.dataset || field.dataset.slugAuto === undefined){
            return;
        }
        field.value = komerceSlug(field.value);
        if(field.value === ''){
            field.dataset.slugAuto = '1';
            var name = document.querySelector('[data-slug-target="' + field.id + '"]');
            if(name){
                field.value = komerceSlug(name.value);
            }
        }
    });
})();

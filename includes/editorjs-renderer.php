<?php
/**
 * EditorJS Renderer - Convert EditorJS JSON to HTML
 */

class EditorJSRenderer
{

    /**
     * Render EditorJS data to HTML
     */
    public static function render($editorData)
    {
        if (empty($editorData) || !isset($editorData['blocks'])) {
            return '';
        }

        $html = '';

        foreach ($editorData['blocks'] as $block) {
            $html .= self::renderBlock($block);
        }

        return $html;
    }

    /**
     * Render individual block
     */
    private static function renderBlock($block)
    {
        $type = $block['type'] ?? '';
        $data = $block['data'] ?? [];

        switch ($type) {
            case 'header':
                return self::renderHeader($data);
            case 'paragraph':
                return self::renderParagraph($data);
            case 'list':
                return self::renderList($data);
            case 'image':
                return self::renderImage($data);
            case 'quote':
                return self::renderQuote($data);
            case 'code':
                return self::renderCode($data);
            case 'embed':
                return self::renderEmbed($data);
            case 'table':
                return self::renderTable($data);
            case 'delimiter':
                return self::renderDelimiter();
            case 'warning':
                return self::renderWarning($data);
            case 'checklist':
                return self::renderChecklist($data);
            default:
                return '';
        }
    }

    /**
     * Render header block
     */
    private static function renderHeader($data)
    {
        $level = $data['level'] ?? 2;
        $text = $data['text'] ?? '';
        return "<h{$level} class=\"news-heading\">{$text}</h{$level}>\n";
    }

    /**
     * Render paragraph block
     */
    private static function renderParagraph($data)
    {
        $text = $data['text'] ?? '';
        return "<p class=\"news-paragraph\">{$text}</p>\n";
    }

    /**
     * Render list block
     */
    private static function renderList($data)
    {
        $style = $data['style'] ?? 'unordered';
        $items = $data['items'] ?? [];

        $tag = $style === 'ordered' ? 'ol' : 'ul';
        $html = "<{$tag} class=\"news-list\">\n";

        foreach ($items as $item) {
            $html .= "<li>{$item}</li>\n";
        }

        $html .= "</{$tag}>\n";
        return $html;
    }

    /**
     * Render image block
     */
    private static function renderImage($data)
    {
        $url = $data['file']['url'] ?? '';
        $caption = $data['caption'] ?? '';
        $stretched = $data['stretched'] ?? false;
        $withBorder = $data['withBorder'] ?? false;
        $withBackground = $data['withBackground'] ?? false;

        $classes = ['news-image'];
        if ($stretched)
            $classes[] = 'stretched';
        if ($withBorder)
            $classes[] = 'with-border';
        if ($withBackground)
            $classes[] = 'with-background';

        $classAttr = implode(' ', $classes);

        $html = "<figure class=\"{$classAttr}\">\n";
        $html .= "<img src=\"{$url}\" alt=\"{$caption}\" loading=\"lazy\">\n";
        if ($caption) {
            $html .= "<figcaption>{$caption}</figcaption>\n";
        }
        $html .= "</figure>\n";

        return $html;
    }

    /**
     * Render quote block
     */
    private static function renderQuote($data)
    {
        $text = $data['text'] ?? '';
        $caption = $data['caption'] ?? '';
        $alignment = $data['alignment'] ?? 'left';

        $html = "<blockquote class=\"news-quote text-{$alignment}\">\n";
        $html .= "<p>{$text}</p>\n";
        if ($caption) {
            $html .= "<cite>{$caption}</cite>\n";
        }
        $html .= "</blockquote>\n";

        return $html;
    }

    /**
     * Render code block
     */
    private static function renderCode($data)
    {
        $code = htmlspecialchars($data['code'] ?? '');
        return "<pre class=\"news-code\"><code>{$code}</code></pre>\n";
    }

    /**
     * Render embed block
     */
    private static function renderEmbed($data)
    {
        $service = $data['service'] ?? '';
        $embed = $data['embed'] ?? '';
        $caption = $data['caption'] ?? '';

        $html = "<div class=\"news-embed news-embed-{$service}\">\n";
        $html .= "<div class=\"embed-container\">{$embed}</div>\n";
        if ($caption) {
            $html .= "<p class=\"embed-caption\">{$caption}</p>\n";
        }
        $html .= "</div>\n";

        return $html;
    }

    /**
     * Render table block
     */
    private static function renderTable($data)
    {
        $content = $data['content'] ?? [];
        $withHeadings = $data['withHeadings'] ?? false;

        $html = "<div class=\"news-table-wrapper\">\n<table class=\"news-table\">\n";

        foreach ($content as $rowIndex => $row) {
            $tag = ($withHeadings && $rowIndex === 0) ? 'th' : 'td';
            $html .= "<tr>\n";
            foreach ($row as $cell) {
                $html .= "<{$tag}>{$cell}</{$tag}>\n";
            }
            $html .= "</tr>\n";
        }

        $html .= "</table>\n</div>\n";
        return $html;
    }

    /**
     * Render delimiter block
     */
    private static function renderDelimiter()
    {
        return "<div class=\"news-delimiter\">* * *</div>\n";
    }

    /**
     * Render warning block
     */
    private static function renderWarning($data)
    {
        $title = $data['title'] ?? '';
        $message = $data['message'] ?? '';

        $html = "<div class=\"news-warning\">\n";
        if ($title) {
            $html .= "<strong>{$title}</strong>\n";
        }
        $html .= "<p>{$message}</p>\n";
        $html .= "</div>\n";

        return $html;
    }

    /**
     * Render checklist block
     */
    private static function renderChecklist($data)
    {
        $items = $data['items'] ?? [];

        $html = "<ul class=\"news-checklist\">\n";
        foreach ($items as $item) {
            $checked = $item['checked'] ?? false;
            $text = $item['text'] ?? '';
            $checkedClass = $checked ? 'checked' : '';
            $html .= "<li class=\"{$checkedClass}\">{$text}</li>\n";
        }
        $html .= "</ul>\n";

        return $html;
    }
}

<?php

/**
 * This class currently has no functions, but exists to provide documentation
 * for the model template pattern and its interfaces.
 *
 * When creating new model specs, the simplest spec is an object with a class
 * name and a unique ID. The spec can be passed into an editor for a user to
 * edit. For more complex models, there will be various options that provide
 * good starting places for users. Page models are a good example of this. You
 * may want to offer users a template for a blog post, a template for a press
 * release, or a template for a product page. These templates will contain
 * different page kind value, different layouts, and different sets of default
 * views.
 *
 * For each template you should create a class. Each template class should
 * implement two interfaces:
 *
 *      CBModelTemplate_spec()
 *
 *          This function returns the spec, without an ID, that is the starting
 *          point for the model.
 *
 *      CBModelTemplate_title()
 *
 *          This function returns a name for the template, such as "Blog Post"
 *          or "Product Page".
 *
 * The template classes CBBlogPostPageTemplate, CBEmptyPageTemplate, and
 * CBStandardPageTemplate provide examples of this pattern.
 *
 * Page models can technically be of many different class names. The templates
 * that could be potentially useful for pages are diverse. When and if there is
 * a second model area that is similar to pages there will be more opportunity
 * to further develop the template pattern.
 */
final class CBModelTemplate {

}

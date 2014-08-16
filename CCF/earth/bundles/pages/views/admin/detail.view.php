<ul class="page-type-tabs clearfix">
{% each Earth\Pages\Page::$_types as $type %}
	<li {{ $page->type == $type ? 'class="active"' : '' }}>
		<a style="width: {{ 100 / count( Earth\Pages\Page::$_types ) }}%;" class="panel-ajax-trigger" href="{{CCUrl::action( 'change_type', array( ':fingerprint', 'r' => $page->id, 'type' => $type ) )}}">{{__('Earth\\Pages::model/page.type.'.$type)}}</a>
	</li>
{% endeach %}
</ul>
<div class="page-detail-edit pdn15">
	{{UI\Form::start( 'page-detail', array( 'method' => 'post', 'class' => 'panel-form', action => CCUrl::action( 'edit' ) ) )}}
		{{$edit_view}}
		{{UI\Form::input( 'r', $page->id, 'hidden' )}}
	{{UI\Form::end()}}
</div>
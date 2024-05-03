<div x-data="{ showMsg: false }">
    <div wire:ignore>
        <div id="{{ $editorId }}" class="min-h-32 rounded rounded-t-none"></div>
        <div id="{{ $toolbarId }}" class="w-full"></div>
    </div>
    <div class="min-h-6">
        @if ($errors->has('text'))
            <span class="align-top text-xs text-red-500 sm:text-sm">
                {{ __($errors->first('text')) }}
            </span>
        @endif
    </div>

    <div
        x-show="!showMsg"
        x-transition
        @reply-updated.window="(e) => {
                let key = @js($reply->getKey());
                if(e.detail.replyId === key && $wire.approvalRequired) {
                    showMsg = true;
                }
            }"
        class="space-x-4"
    >
        <x-comments::button wire:click="save" size="sm" dirtyTarget="text" loadingTarget="save">
            Save
        </x-comments::button>
        <x-comments::button wire:click="discard" size="sm" severity="info" type="button" loadingTarget="discard">
            Discard
        </x-comments::button>
    </div>

    <div x-show="showMsg" x-transition>
        <span x-transition class="align-top text-xs text-green-500 sm:text-sm">
            {{ __('Reply updated and will be displayed once approved !') }}
        </span>
    </div>
</div>

@script
    <script>
        let editorConfig = @js(config('comments.editor_config'));
        const quill = new Quill(`#${$wire.editorId}`, editorConfig);

        const editorElm = document.querySelector(`#${$wire.editorId} .ql-editor`);
        const toolbarParentElm = document.querySelector(`#${$wire.toolbarId}`);

        const toolbars = Array.from(document.querySelector('.ql-toolbar'));

        toolbarParentElm.append(toolbars.slice(-1));

        editorElm.innerHTML = $wire.text;

        quill.on('text-change', (delta, oldDelta, source) => {
            let html = editorElm.innerHTML;
            if (html === '<p><br></p>') {
                $wire.text = '';
                return;
            }
            $wire.text = html;
        });

        $wire.on('reply-update-discarded', function () {
            editorElm.innerHTML = @js($reply->text);
        });

    </script>
@endscript

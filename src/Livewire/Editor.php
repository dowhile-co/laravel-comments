<?php

namespace LakM\Comments\Livewire;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use LakM\Comments\Exceptions\InvalidEditorIDException;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class Editor extends Component
{
    #[Modelable]
    public string $text = '';

    public string $objectNumberName;

    public string $objectName;

    public string $teamId;
    public string $id;

    public string $editorId;
    public string $toolbarId;

    public bool $guestModeEnabled;

    public bool $disableEditor;

    /**
     * @throws \Throwable
     */
    public function mount(string $editorId, bool $guestModeEnabled, bool $disableEditor = false, string $objectNumberName = '', string $teamId = '', string $objectName =''): void
    {
        throw_unless(Str::isUuid($editorId), InvalidEditorIDException::make());

        $this->editorId = 'editor-' . $editorId;
        $this->toolbarId = 'toolbar-' . $editorId;

        $this->id = $editorId;

        $this->guestModeEnabled = $guestModeEnabled;

        $this->disableEditor = $disableEditor;
        
        $this->objectNumberName = $objectNumberName;

        $this->objectName = $objectName;

        $this->teamId = $teamId;
    }

    public function render(): View|Factory|Application
    {
        return view('comments::livewire.editor');
    }
}

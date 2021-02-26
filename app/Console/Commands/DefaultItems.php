<?php

namespace App\Console\Commands;

use App\Models\Component;
use App\Models\Metal;
use App\Models\ResourceType;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class DefaultItems extends Command
{
    /** @var Collection<Metal> */
    private Collection $metals;
    private array $typesId = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipe:default';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default recipie';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->metals = Metal::all();

        $this->addIgnots();
        $this->addLists();
        $this->addWires();
        $this->addCabels();
        $this->addCogs();
        $this->addSticks();

        return 0;
    }

    private function getType($name)
    {
        $type = ResourceType::firstOrNew(['name' => $name]);
        if (!$type->exists()) {
            $type->save();
        }

        $this->typesId[$name] = $type->id;
        return $type->id;
    }

    private function getMetal($name)
    {
        return collect($this->metals)->first(fn($el) => $el->name === $name)->id;
    }

    private function newComponent($metalId, $typeId): Component
    {
        $component = new Component();
        $component->metal_id = $metalId;
        $component->type_id = $typeId;
        $component->tier = 0;
        $component->save();
        return $component;
    }



    private function addIgnots()
    {
        $type = $this->getType('Слиток');
        foreach ($this->metals as $metal) {
            $this->newComponent($metal->id, $type);
        }
    }

    private function addLists()
    {
        $ignots = collect(Component::where('type_id', $this->typesId['Слиток'])->get());
        $type = $this->getType('Пластина');
        foreach ($this->metals as $metal) {
            $component = $this->newComponent($metal->id, $type);

            $ignot = $ignots->first(fn($el) => $el->metal_id === $metal->id);
            $component->addElement($ignot->id, 1);
        }
    }

    private function addWires()
    {
        $use = [
            'Олово',
            'Медь',
            'Платина',
            'Золото',
            'Электриум',
            'Вольфрам',
            'Вольфрамовая сталь',
            'Кобальт',
            'Свинец',
            'Титан',
            'Железо',
            'Сталь',
            'Нихром',
            'Алюминий',
            'Серебро',
        ];
        $ignots = collect(Component::where('type_id', $this->typesId['Слиток'])->get());
        $type = $this->getType('Провод 1х');
        foreach ($this->metals as $metal) {
            if (!in_array($metal->name, $use)) {
                continue;
            }
            $component = $this->newComponent($metal->id, $type);

            $ignot = $ignots->first(fn($el) => $el->metal_id === $metal->id);
            $component->addElement($ignot->id, 0.5);
        }
    }

    private function addCabels()
    {
        $use = [
            'Олово',
            'Медь',
            'Платина',
            'Золото',
            'Электриум',
            'Вольфрам',
            'Вольфрамовая сталь',
            'Кобальт',
            'Свинец',
            'Титан',
            'Железо',
            'Сталь',
            'Нихром',
            'Алюминий',
            'Серебро',
        ];

        $whires = collect(Component::where('type_id', $this->typesId['Провод 1х'])->get());
        $rubber = Component::where('type_id', $this->typesId['Слиток'])->where('metal_id', $this->getMetal('Резина'))->first();
        $type = $this->getType('Кабель 1х');
        foreach ($this->metals as $metal) {
            if (!in_array($metal->name, $use)) {
                continue;
            }
            $component = $this->newComponent($metal->id, $type);

            $whire = $whires->first(fn($el) => $el->metal_id === $metal->id);
            $component->addElement($whire->id, 1);
            $component->addElement($rubber->id, 1);
        }
    }

    private function addCogs()
    {
        $ignots = collect(Component::where('type_id', $this->typesId['Пластина'])->get());
        $type = $this->getType('Шестерня');
        foreach ($this->metals as $metal) {
            $component = $this->newComponent($metal->id, $type);

            $ignot = $ignots->first(fn($el) => $el->metal_id === $metal->id);
            $component->addElement($ignot->id, 1);
        }
    }

    private function addSticks()
    {

        $ignots = collect(Component::where('type_id', $this->typesId['Слиток'])->get());
        $type = $this->getType('Стержень');
        foreach ($this->metals as $metal) {
            $component = $this->newComponent($metal->id, $type);

            $ignot = $ignots->first(fn($el) => $el->metal_id === $metal->id);
            $component->addElement($ignot->id, 0.5);
        }
    }

}

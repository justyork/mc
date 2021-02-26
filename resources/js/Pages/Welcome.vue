<template>
    <div class="">
        <div class="flex">

            <div class=" p-4 flex-auto">
                <div class="border-b border-gray-300 pb-2 pr-2">
                    <div class="cursor-pointer hover:text-blue-500 " @click="showAddComponent = !showAddComponent">
                    <span v-if="!showAddComponent">
                        <fa icon="plus"  />
                        Добавить рецепт
                    </span>
                        <span v-else>
                        <fa icon="times"  />
                        Закрыть
                    </span>
                    </div>
                    <add-component :metals="metals"
                                   :types="types"
                                   :component-list="components.data"
                                   :tier-list="tierList"
                                   v-if="showAddComponent"
                    />
                </div>

                <div class="flex w-full p-4">
                    <div class="flex-auto">
                        <calculator :tier-list="tierList" :component-list="components.data" />
                    </div>
                </div>
            </div>
            <div  style="max-width: 300px;">
                <h3 class="text-xl font-bold">Компоненты в системе</h3>
                <ul v-if="sortComponents()" class="overflow-y-scroll h-full" style="max-height: 600px;">
                    <li v-for="(el) in components.data">
                        <recipe-name :item="el" />
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<style scoped>
.btn {
    @apply text-white bg-green-400 px-2 py-1 rounded;
}
</style>

<script>
import AddComponent from "../components/AddComponent";
import CalculatorComponent from "../components/CalculatorComponent";
import RecipeName from "@/components/RecipeName";

    export default {
        props: {
            canLogin: Boolean,
            canRegister: Boolean,
            laravelVersion: String,
            phpVersion: String,
            components: Array,
            metals: Array,
            types: Array,
        },
        components: {
            RecipeName,
            calculator:CalculatorComponent,
            AddComponent,
        },
        methods: {

            getName(el) {
                return el.name + (el.tier ? ' ('+this.tierList[el.tier]+')' : '');
            },
            mapParams(items) {
                let arr = {};
                items.forEach((item) => {
                    arr[item.id] = item.name
                })
                return arr;
            },
            sortComponents() {
                let components = this.components.data;
                components.sort(function(a,b) {
                    let x = a.fullName.toLowerCase();
                    let y = b.fullName.toLowerCase();
                    return x < y ? -1 : x > y ? 1 : 0;
                });

                return components
            }
        },
        data() {
            return {
                tierList: [
                    'None',
                    '8 ULV',
                    '32 LV',
                    '128 MV',
                    '512 HV',
                    '2048 EV',
                    '8192 IV',
                    '32768 LuV',
                    '131072 ZPM',
                    '524288 UV'
                ],
                showAddComponent: false
            };
        },
        created() {
            localStorage.setItem('metals', JSON.stringify(this.mapParams(this.metals)));
            localStorage.setItem('types', JSON.stringify(this.mapParams(this.types)));
            localStorage.setItem('tierList', JSON.stringify(this.tierList));
        }
    }
</script>

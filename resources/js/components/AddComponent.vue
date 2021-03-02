<template>
    <form @submit.prevent="submit">
        <div class="flex">
            <div class="flex">
                <div class="cursor-pointer mt-2">
                    <div :className="(selectInputType ? 'text-blue-500' : '') + ' w-8'">
                        <fa icon="caret-square-down" @click="selectInputType = true"  />
                    </div>
                    <div :className="(!selectInputType ? 'text-blue-500' : '') + ' w-8'">
                        <fa icon="i-cursor"  @click="selectInputType = false"  />
                    </div>
                </div>

                <div v-if="selectInputType" class="flex">
                    <div>
                        <jet-label for="name" value="Метал" />
                        <select v-model="form.metalId" class="w-40 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block">
                            <option v-for="metal in metals" :value="metal.id">{{ metal.name }}</option>
                        </select>
                    </div>
                    <div>
                        <jet-label for="name" value="Тип" />
                        <select v-model="form.typeId"  class="w-40 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block">
                            <option v-for="type in types" :value="type.id">{{ type.name }}</option>
                        </select>
                    </div>
                </div>
                <div v-else>
                    <jet-label for="name" value="Название" />
                    <div class="flex">
                        <jet-input id="name" type="text" class="mt-1 block" v-model="form.name" required autofocus autocomplete="name" />
                    </div>
                </div>
                <div class="ml-2 mt-8" v-if="form.name !== '' || (form.metalId && form.typeId)">
                    <fa icon="sync" v-if="existsIndex !== -1" />
                    <fa icon="check-circle" v-else />
                </div>
            </div>
            <div class="ml-4">
                <jet-label for="tier" value="Тир" />
                <select name="tier" id="tier" v-model="form.tier" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block">
                    <option v-for="(name, key) in tierList"
                            :key="key"
                            :value="key">{{ name }}</option>
                </select>
            </div>
        </div>
        <span class="text-sm cursor-pointer" @click="addComponent">
            <fa icon="plus" class="mr-2"/>
            Добавить элемент
        </span>
        <div v-if="elementList">
            <div v-for="(component, key) in elementList" class="flex">
                <select v-model="component.component_id" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block">
                    <option v-for="item in componentList" :value="item.id"><recipe-name :item="item"/></option>
                </select>
                <jet-input type="text" class="ml-2 mt-1 block w-16" v-model="component.count" />
                <div class="w-8 ml-2 mt-3 cursor-pointer hover:text-red-500">
                    <fa icon="trash" @click="removeComponent(key)"/>
                </div>
            </div>
        </div>
        <div>
            <jet-button class="mt-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Сохранить
            </jet-button>
        </div>
    </form>
</template>

<script>
import JetButton from '@/Jetstream/Button'
import JetInput from '@/Jetstream/Input'
import JetLabel from '@/Jetstream/Label'
import RecipeName from "@/components/RecipeName";
    export default {
        props: {
            'tierList': Array,
            'componentList': Array,
            'metals': Array,
            'types': Array
        },
        components: {
            RecipeName,
            JetButton,
            JetInput,
            JetLabel,
        },
        computed: {
            existsIndex() {
                return this.componentList.findIndex((el) =>  {
                    if (el.name) {
                        return el.name.toLowerCase() === this.form.name.toLowerCase() && el.tier === this.form.tier
                    } else {
                        return el.metal_id === this.form.metalId && el.type_id === this.form.typeId;
                    }
                });
            },
            elementList() {
                let elements = this.form.components;

                let index = this.existsIndex

                if (index !== -1) {
                    elements = this.componentList[index].elements
                    this.form.components = elements
                }

                return elements;
            }
        },
        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                    tier: 0,
                    components: [],
                    metalId: 0,
                    typeId: 0
                }),
                selectInputType: true,
            };
        },
        methods: {
            removeComponent(index) {
                this.form.components.splice(index, 1);
            },
            getName(el) {
                return el.name + (el.tier ? ' ('+this.tierList[el.tier]+')' : '');
            },
            addComponent() {
                this.form.components.push({
                    component_id: null,
                    count: 1
                });
            },
            submit() {
                if (this.form.name.trim() === '' && (this.form.metalId === 0 || this.form.typeId === 0)) {
                    return;
                }

                this.form.put('/', {
                    onFinish: () => {
                        this.form.reset('name', 'tier', 'metalId', 'typeId');
                        this.form.components = [];
                    },
                    preserveScroll: false,
                    resetOnSuccess: false,
                })
            }
        },
    }
</script>

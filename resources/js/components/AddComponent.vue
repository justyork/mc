<template>
    <form @submit.prevent="submit">
        <div class="flex">
            <div>
                <jet-label for="name" value="Name" />
                <div class="flex">
                    <jet-input id="name" type="text" class="mt-1 block" v-model="form.name" required autofocus autocomplete="name" />
                    <div class="ml-2 pt-2" v-if="form.name !== ''">
                        <fa icon="sync" v-if="existsValue" />
                        <fa icon="check-circle" v-else />
                    </div>
                </div>
            </div>
            <div class="ml-4">
                <jet-label for="tier" value="Tier" />
                <select name="tier" id="tier" v-model="form.tier" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block">
                    <option v-for="(name, key) in tierList"
                            :key="key"
                            :value="key">{{ name }}</option>
                </select>
            </div>
        </div>
        <span class="text-sm cursor-pointer" @click="addComponent">
            <fa icon="plus" class="mr-2"/>
            Add element
        </span>
        <div v-if="form.components">
            <div v-for="component in form.components" class="flex">
                <select v-model="component.component_id" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block">
                    <option v-for="item in componentList" :value="item.id">{{ item.name }}</option>
                </select>
                <jet-input type="text" class="ml-2 mt-1 block w-16" v-model="component.count" />
            </div>
        </div>
        <div>
            <jet-button class="mt-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </jet-button>
        </div>
    </form>
</template>

<script>
import JetButton from '@/Jetstream/Button'
import JetInput from '@/Jetstream/Input'
import JetLabel from '@/Jetstream/Label'
    export default {
        props: {
            'tierList': Array,
            'componentList': Array,
        },
        components: {
            JetButton,
            JetInput,
            JetLabel,
        },
        computed: {
            existsValue() {
                return this.componentList.findIndex((el) => el.name.toLowerCase() === this.form.name.toLowerCase()) !== -1;
            }
        },
        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                    tier: 0,
                    components: []
                }),

            };
        },
        methods: {
            addComponent() {
                this.form.components.push({
                    component_id: null,
                    count: 1
                });
            },
            submit() {
                if (this.form.name.trim() === '') {
                    return;
                }

                this.form.put('/api/component', {
                    onFinish: () => {
                        this.form.reset('name', 'tier');
                        this.form.components = [];
                    }
                })
            }
        },
    }
</script>

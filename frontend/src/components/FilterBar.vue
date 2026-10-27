<script setup>
const props = defineProps({
  filters: { type: Object, required: true },
  isFiltered: { type: Boolean, default: false },
  topics: { type: Array, required: true },
})
const emit = defineEmits(['update:filters', 'apply', 'clear'])

function update(patch) {
  emit('update:filters', { ...props.filters, ...patch })
}

function onSearchInput(e) {
  update({ search: e.target.value })
  emit('apply')
}

function onChange() {
  emit('apply')
}
</script>

<template>
  <div class="bg-white rounded-xl shadow-sm p-4 flex flex-col md:flex-row gap-3">
    <input
      type="text"
      placeholder="Buscar recursos por título o extracto..."
      :value="filters.search"
      @input="onSearchInput"
      class="flex-1 border border-slate-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal"
    />

    <select
      :value="filters.topic"
      @change="update({ topic: $event.target.value }); onChange()"
      class="border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal"
    >
      <option value="">Todos los temas</option>
      <option v-for="t in topics" :key="t.slug" :value="t.slug">{{ t.name }}</option>
    </select>

    <select
      :value="filters.sort"
      @change="update({ sort: $event.target.value }); onChange()"
      class="border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal"
    >
      <option value="recent">Más recientes</option>
      <option value="old">Más antiguos</option>
    </select>

    <button
      v-if="isFiltered"
      type="button"
      @click="emit('clear')"
      class="text-sm text-slate-600 hover:text-brand-dark underline"
    >
      Limpiar filtros
    </button>
  </div>
</template>
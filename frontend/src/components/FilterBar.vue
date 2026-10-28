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
  <div class="bg-white rounded-xl shadow-sm p-3 sm:p-4 flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-3">
    <input
      type="text"
      placeholder="Buscar recursos por título o extracto..."
      :value="filters.search"
      @input="onSearchInput"
      class="w-full sm:flex-1 sm:min-w-[16rem] border border-slate-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal"
    />

    <div class="grid grid-cols-1 min-[480px]:grid-cols-2 sm:flex sm:flex-row gap-3">
      <select
        :value="filters.topic"
        @change="update({ topic: $event.target.value }); onChange()"
        class="w-full sm:w-auto border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal"
      >
        <option value="">Todos los temas</option>
        <option v-for="t in topics" :key="t.slug" :value="t.slug">{{ t.name }}</option>
      </select>

      <select
        :value="filters.sort"
        @change="update({ sort: $event.target.value }); onChange()"
        class="w-full sm:w-auto border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal"
      >
        <option value="recent">Más recientes</option>
        <option value="old">Más antiguos</option>
      </select>
    </div>

    <button
      v-if="isFiltered"
      type="button"
      @click="emit('clear')"
      class="self-start sm:self-center py-1 text-sm text-slate-600 hover:text-brand-dark underline"
    >
      Limpiar filtros
    </button>
  </div>
</template>
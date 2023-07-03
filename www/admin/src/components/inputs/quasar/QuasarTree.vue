<script setup lang="ts">
import { OptionsCallback, OptionSelectedHook } from 'src/classes/inputs/quasar/quasar-tree'
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { QTree, QTreeNode, QTreeProps } from 'quasar'

interface Props {
  modelValue?: number | string
  label?: string
  optionsCallback: OptionsCallback
  optionSelectedHook: OptionSelectedHook
  params?: QTreeProps
}

const { t } = useI18n()

const props = defineProps<Props>()

const emit = defineEmits<{ (e: 'update:modelValue', value: number | string): void }>()

const filter = ref('')
const filterRef = ref<InstanceType<typeof HTMLInputElement> | null>(null)
const treeRef = ref<InstanceType<typeof QTree> | null>(null)
const options = ref(props.optionsCallback())
const selected = ref(props.modelValue || null)
const selectedNode = ref<QTreeNode | undefined>()
const treeOpened = ref(false)

watch(
  () => props.modelValue,
  () => {
    selected.value = props.modelValue || null
  },
  { deep: true }
)
watch(
  selected,
  () => {
    if (selected.value) {
      selectedNode.value = getNodeById(selected.value)
      emit('update:modelValue', selected.value)
    }
  },
  { deep: true }
)
watch(
  selectedNode,
  () => {
    props.optionSelectedHook(selectedNode.value)
  },
  { deep: true }
)
const resetFilter = () => {
  filter.value = ''
  if (filterRef.value) {
    filterRef.value.focus()
  }
}

// const onSelected =
const openTree = () => {
  treeOpened.value = true
}
const closeTree = () => {
  treeOpened.value = false
  filter.value = ''
}
const onFilterChange = () => {
  if (!treeRef.value) {
    return
  }
  if (filter.value.length > 3) {
    treeRef.value.expandAll()
  } else {
    treeRef.value.collapseAll()
  }
}
const expandAll = () => {
  if (!treeRef.value) {
    return
  }
  treeRef.value.expandAll()
}
const collapseAll = () => {
  if (!treeRef.value) {
    return
  }
  treeRef.value.collapseAll()
}

const getNodeById = (id: number | string) => {
  if (!selected.value) {
    return undefined
  }
  let searchedNode: QTreeNode | undefined = undefined

  const handleRoot = (option: QTreeNode) => {
    if (searchedNode) {
      return
    }

    if (option[props.params?.nodeKey || 'id'] === id) {
      searchedNode = option
      return
    }

    option.children?.forEach(handleRoot)
  }

  options.value.forEach(handleRoot)

  return searchedNode
}

if (selected.value) {
  selectedNode.value = getNodeById(selected.value)
}
</script>

<template>
  <div>
    <q-input
      :model-value="selectedNode?.label"
      readonly
      :label="label"
      class="tw-cursor-pointer"
      @click="openTree"
    />
    <q-dialog v-model="treeOpened" position="bottom">
      <q-card style="width: 350px">
        <q-input
          ref="filterRef"
          v-model="filter"
          filled
          :label="t('texts.search')"
          @update:model-value="onFilterChange"
        >
          <template #append>
            <q-icon v-if="filter !== ''" name="clear" class="cursor-pointer" @click="resetFilter" />
          </template>
        </q-input>

        <q-card-section>
          <q-btn-group push spread>
            <q-btn push label="Открыть все" @click="expandAll" />
            <q-btn push label="Закрыть все" @click="collapseAll" />
          </q-btn-group>
        </q-card-section>

        <q-card-section class="row items-center no-wrap">
          <q-tree
            ref="treeRef"
            v-bind="params"
            v-model:selected="selected"
            :nodes="options"
            :filter="filter"
            @update:selected="closeTree"
          />
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

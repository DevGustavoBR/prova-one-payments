<script setup>


const {
  label,
  modelValue,
  type,
  placeholder,
  disabled,
  error,
  inputClass,
} = defineProps({
  label: { type: String, required: true },
  modelValue: { type: [String, Number], default: "" },
  type: { type: String, default: "text" },
  placeholder: { type: String, default: "" },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: "" },
  inputClass: {
    type: String,
    default: "px-2 pb-2 bg-white text-gray-800 w-full text-sm border-t-0 border-l-0 border-r-0 border-b focus:border-blue-600 outline-none",
  },
});

const emit = defineEmits(["update:modelValue", "blur", "input"]);


const handleInput = (event) => {
  emit("update:modelValue", event.target.value);
  emit("input", event);
};

const handleBlur = (event) => {
  emit("blur", event);
};

</script>


<template>
 <label v-if="label" class="block text-gray-700 text-sm font-medium mb-1">{{ label }}</label>
    <input
      :type="type"
      :class="inputClass"
      :placeholder="placeholder"
      :disabled="disabled"
      :value="modelValue"
      @input="handleInput"
      @blur="handleBlur"
    />
    <span v-if="error" class="text-red-500 text-xs mt-1">{{ error }}</span>
</template>

<template>
	<div class="option-list">
		<span
			:class="{
				'option-item': true,
				'active': selected.indexOf(item.value) >= 0
			}"
			v-for="(item, index) in options"
			:key="index"
			@click="handleSelect(item)"
		>
			{{item.label}}
		</span>
	</div>
</template>
<script>
export default {
	props: {
		options: {
			type: Array,
			default() {
				return [];
			}
		},
		value: {
			type: Array,
			default() {
				return [];
			}
		},
		multiple: {
			type: Boolean,
			default: false
		},
		removeSelectOption: {
			type: String,
			default: ''
		}
	},
	data() {
		return {
			// 已选条件
			selected: this.value && this.value.length > 0 ? this.value : []
		};
	},
	methods: {
		// 处理选择条件
		handleSelect(item) {
			let index = this.selected.indexOf(item.value);

			// 如果存在，删除
			if (index >= 0) {
				if (this.selected.length > 1) {
					this.selected.splice(index, 1);
				}
			} else {
				// 多选条件
				if (this.selected.length === 0 || this.multiple) {
					this.selected.push(item.value);
				} else {
					// 单选条件
					this.selected = [item.value];
				}
			}
			this.$emit('update:value', this.selected);
			this.$emit('onChange', this.selected);
		}
	},
	watch: {
		value() {
			this.selected = this.value && this.value.length > 0 ? this.value : [];
		},
		removeSelectOption(newV) {
			let value = Number(newV);
			let removeValue = this.selected.indexOf(value);

			this.selected.splice(removeValue, 1);
		}
	}
};
</script>

<style lang="less" scoped>
@import "~assets/css/common_avairail.less";
.option-item {
  color: #818181;
  font-size: 14px;
  margin-right: 16px;
  cursor: pointer;
  &:hover {
    color: @primaryColor;
  }
  &.active {
    color: @primaryColor;
  }
}
</style>


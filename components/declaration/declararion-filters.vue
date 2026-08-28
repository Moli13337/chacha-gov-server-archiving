<template>
	<div class="filters-container">
		<div class="filter-panel">
			<div
				class="selected-options"
				v-if="selectedFilterOptions.length > 0"
			>
				<filter-item label='已选条件'>
					<el-tag
						closable
						effect='plain'
						v-for="(item, index) in selectedFilterOptions"
						:key="index"
						@close="handleFilterRemove(item)"
					>
						{{item.label}}
					</el-tag>
				</filter-item>
			</div>
			<div class="filter-options">
				<filter-item label='项目状态'>
					<text-select
						:removeSelectOption="removeSelectOption"
						:options="reportStatusList"
						:value="value"
						@onChange="handleFilterChange('reportStatus', $event)"
					/>
				</filter-item>
				<filter-item label='政策类型'>
					<el-select
						class="mold_type"
						placeholder="请选择"
						v-model="filters.moldType"
						@change="handleFilterChange('moldType', $event)"
					>
						<el-option
							v-for="(item, index) in moldTypeList"
							:key="index"
							:label="item.name"
							:value="item.id"
						>{{item.name}}</el-option>
					</el-select>
				</filter-item>
				<filter-item label='适用地区'>
					<el-select
						placeholder="请选择"
						v-model="filters.suitableRegion"
						@change="handleFilterChange('suitableRegion', $event)"
						disabled
					>
						<el-option
							v-for="(item, index) in suitableRegionList"
							:key="index"
							:label="item.label"
							:value="item.value"
						/>
					</el-select>
				</filter-item>
			</div>
		</div>
	</div>
</template>

<script>
import FilterItem from '@/components/filter-item';
import TextSelect from '@/components/text-select';
export default {
	components: {
		FilterItem,
		TextSelect
	},
	props: {
		filters: {
			type: Object,
			default() {
				return {};
			}
		},
		moldTypeList: {
			type: Array,
			default() {
				return [];
			}
		}
	},
	data() {
		return {
			value: [1],
			removeSelectOption: '',
			reportStatusList: [
				{
					value: 1,
					label: '申报中'
				},
				{
					value: 2,
					label: '即将申报'
				},
				{
					value: 3,
					label: '申报已截止'
				}
			],
			suitableRegionList: [
				{
					value: 510115000000,
					label: '成都市温江区'
				},
				{
					value: 510100000000,
					label: '四川省成都市'
				},
				{
					value: 510000000000,
					label: '四川省'
				}
			]
		};
	},
	computed: {
		// 已选条件
		selectedFilterOptions() {
			let selectedOptions = [];

			for (let key in this.filters) {
				let value = this.filters[key];

				console.log('estvalue', value);

				if (value) {
					switch (key) {
						case 'reportStatus':
							selectedOptions.push({
								key: 'reportStatus',
								label: '申报状态：' + this.reportStatusList
									.filter(item => value.indexOf(item.value) !== -1)
									.map(item => item.label)
									.join('，'),
								value: this.reportStatusList
									.filter(item => value.indexOf(item.value) !== -1)
									.map(item => item.value)
									.join('，')
							});
							break;
						case 'moldType':
							if (this.moldTypeList && this.moldTypeList.length > 0) {
								selectedOptions.push({
									key: 'moldType',
									label: '政策类型：' + this.moldTypeList.find(item => item.id === value).name
								});
							}
							break;
						case 'suitableRegion':
							selectedOptions.push({
								key: 'suitableRegion',
								label: '适用地区：' + this.suitableRegionList.find(item => item.value === value).label
							});
							break;
						default:
							break;
					}
				}
			}

			return selectedOptions;
		}
	},
	methods: {
		// 处理过滤条件变化
		handleFilterChange(type, value) {
			console.log('type', type);
			console.log('value', value);
			let filters = {
				...this.filters,
				[type]: value
			};

			this.$emit('update:filters', filters);
			this.$emit('onChange', filters);
		},

		// 删除已选条件
		handleFilterRemove(item) {
			console.log('item', item);
			// 适用地区默认温江区不可更改
			if (item.label == '适用地区：成都市温江区') {
				return;
			}

			// 删除后清除已选条件
			let filters = {
				...this.filters,
				[item.key]: null
			};

			this.$emit('update:filters', filters);
			this.$emit('onChange', filters);
			this.removeSelectOption = item.value;
		}
	}
};
</script>

<style lang="less">
@import "~assets/css/common_avairail.less";
.filters-container {
  display: flex;
  flex-direction: column;
  .selected-options {
    border-bottom: 1px dashed #DCDCDC;
    .el-tag {
      margin: 4px 8px;
    }
  }
  .filter-item {
    .el-input__inner {
      border-radius: 0;
    }
  }
  .filter-panel {
    padding: 16px;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
    background: @backGroundColor;
  }
  .mold_type {
    .el-input__inner {
      width: 600px;
    }
  }
}
</style>

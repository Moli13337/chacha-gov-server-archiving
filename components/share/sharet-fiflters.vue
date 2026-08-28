<template>
	<div class="filters-container">
		<div class="filter-panel">
			<div
				class="selected-options"
				v-if="selectedFilterOptions && selectedFilterOptions.length > 0"
			>
				<filter-item>
					<p class="fiflter-tips">为你找到
						<span
							class="blue"
							v-for="(item, index) in selectedFilterOptions"
							:key="index"
						>{{item.label}}
						</span>
						相关信息，筛选共<span class="blue"> {{agentTotal}} </span>项活动
					</p>
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
				<filter-item label='时段'>
					<div style="display: flex; justify-content: space-between;">
						<text-select
							:value="filters.selectTime"
							:options="timeSlot"
							@onChange="handleFilterChange('selectTime', $event)"
						/>
					</div>
				</filter-item>
				<filter-item label='活动状态'>
					<div style="display: flex; justify-content: space-between;">
						<text-select
							:value="filters.status"
							:options="activityStatus"
							@onChange="handleFilterChange('status', $event)"
						/>
					</div>
				</filter-item>
				<filter-item label='报名状态'>
					<div style="display: flex; justify-content: space-between;">
						<text-select
							:value="filters.apply_status"
							:options="applyStatus"
							@onChange="handleFilterChange('apply_status', $event)"
						/>
					</div>
				</filter-item>
				<div
					class="showMoreOption"
				>
					<filter-item label='所在区域'>
						<el-select
							placeholder="请选择"
							v-model="filters.suitableRegion"
							@change="handleFilterChange('suitableRegion', $event)"
						>
							<el-option
								v-for="(item, index) in suitableRegionList"
								:key="index"
								:label="item.district_name"
								:value="item.district_code"
							/>
						</el-select>
					</filter-item>
				</div>
			</div>
		</div>
		<span
			class="hide-more-btn"
			v-if="showMoreOption"
			@click="showMoreOption = false"
		>
			收起
			<i class="el-icon-arrow-up"></i>
		</span>
	</div>
</template>

<script>
import FilterItem from '@/components/filter-item';
import TextSelect from '@/components/text-select';
import {
	applyStatus,
	activityStatus,
	timeSlot
} from '@/pages/share/activity/utils.js';
export default {
	components: {
		FilterItem,
		TextSelect
	},
	props: {
		agentTotal: {
			type: Number,
			default: 0
		},
		agentTypeList: {
			type: Array,
			default() {
				return [];
			}
		},
		filters: {
			type: Object,
			default() {
				return {};
			}
		}
	},
	data() {
		return {
			applyStatus,
			activityStatus,
			timeSlot,
			showMoreOption: true,
			suitableRegionList: [
				{
					district_code: 510115000000,
					district_name: '成都市温江区'
				},
				{
					district_code: 510100000000,
					district_name: '四川省成都市'
				},
				{
					district_code: 510000000000,
					district_name: '四川省'
				},
				{
					district_code: 0,
					district_name: '全国'
				}
			],
			timeList: [

			]
		};
	},
	computed: {

		// 选择过滤条件
		// selectedFilterOptions() {
		// 	let selectedOptions = [];

		// 	for (let key in this.filters) {
		// 		let value = this.filters[key];

		// 		console.log('value', value);
		// 		if (value && value.length > 0) {
		// 			switch (key) {
		// 				case 'type_id':
		// 					selectedOptions.push({
		// 						key: 'type_id',
		// 						label: '服务类型:' + this.agentTypeList
		// 							.filter(item => value.indexOf(item.value) !== -1)
		// 							.map(item => item.label)
		// 							.join('，')
		// 					});
		// 					break;
		// 				case 'suitableRegion':

		// 					selectedOptions.push({
		// 						key: 'suitableRegion',
		// 						label: '适用地区：' + this.suitableRegionList.find(item => item.district_code === value).district_name
		// 					});
		// 					break;
		// 				default:
		// 					break;
		// 			}
		// 		}
		// 	}

		// 	return selectedOptions;
		// }
		// 选择过滤条件
		selectedFilterOptions() {
			let selectedOptions = [];

			for (let key in this.filters) {
				let value = this.filters[key];

				if (!value || (Array.isArray(value) && value.length === 0)) {
					continue;
				}

				switch (key) {
					case 'selectTime':
						selectedOptions.push({
							key: 'selectTime',
							label: '时段:' + this.timeSlot
								.filter(item => value.indexOf(item.value) !== -1)
								.map(item => item.label)
								.join('，'),
						});
						break;
					case 'status':
						selectedOptions.push({
							key: 'status',
							label: '活动状态:' + this.activityStatus
								.filter(item => value.indexOf(item.value) !== -1)
								.map(item => item.label)
								.join('，')
						});
						break;
					case 'apply_status':
						selectedOptions.push({
							key: 'apply_status',
							label: '报名状态:' + this.applyStatus
								.filter(item => value.indexOf(item.value) !== -1)
								.map(item => item.label)
								.join('，')
						});
						break;
					case 'suitableRegion':

						selectedOptions.push({
							key: 'suitableRegion',
							label: '适用地区：' + this.suitableRegionList.find(item => item.district_code === value).district_name
						});
						break;
					default:
						break;
				}
			}

			return selectedOptions;
		}
	},
	methods: {
		// 收起
		handleIndustryShow() {
			this.showMoreOption = false;
		},
		handleFilterChange(type, value) {
			let filters = {
				...this.filters,
				[type]: value
			};

			this.$emit('update:filters', filters);
			this.$emit('onChange', filters);
		},
		handleFilterRemove(item) {
			let filters = {
				...this.filters,
				[item.key]: null
			};

			this.$emit('update:filters', filters);
			this.$emit('onChange', filters);
		}
	},
};
</script>

<style lang="less">
@import "~assets/css/common_avairail.less";
.filters-container {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  .selected-options {
    border-bottom: 1px dashed #DCDCDC;
    .el-tag {
      margin: 4px 8px;
    }
  }
  .filter-panel {
    padding: 16px;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
    background: @backGroundColor;
  }
  .show-more-btn {
    color: @primaryColor;
    font-size: 14px;
    flex-shrink: 0;
    cursor: pointer;
  }
  .hide-more-btn {
    display: inline-block;
    align-self: center;
    width: 100px;
    height: 35px;
    line-height: 35px;
    text-align: center;
    color: #B3B3B3;
    font-size: 14px;
    cursor: pointer;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
    background-color: @backGroundColor;
    i {
      color: #000000;
    }
  }
  .showMoreOption {
    .el-input__inner {
          width: 232px;
          border-radius: 0;
    }
  }
  .fiflter-tips {
    display: inline-block;
  }
  .blue {
    color: @primaryColor;
  }
}
</style>

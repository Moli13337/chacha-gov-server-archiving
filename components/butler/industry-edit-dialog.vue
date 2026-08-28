<template>
	<el-dialog
		title="选择关注的行业"
		:visible.sync="ownerVisible"
		@close="handleDialogClose"
		class="editForm"
		width="45%"
	>
		<div class="explain marginB">
			<img class="small"/>
			<div class="right">
				<div>
					<img
						class="icon_tips"
						src="~assets/images/butler/icon_Tips.png"
					/>
				</div>
				<p>
					企业管家实时为你推送最新政策申报项目信息
					<br/>用户在关注的行业处，选择企业关注的信息标签之后，系统将会推荐企业关注的最新政策申报
				</p>
			</div>
		</div>
		<el-form
			:model="industryForm"
			:rules="rules"
			ref="industryForm"
		>
			<el-form-item
				label="企业所属主行业"
				label-width="150px"
				prop="mainIndustry"
			>
				<el-cascader
					class="industy-selection"
					clearable
					placeholder="点击选择所属行业"
					:options="industryOptions"
					:props="industryProps"
					v-model="industryForm.mainIndustry.industryList"
				/>
			</el-form-item>
			<el-form-item
				label="企业所属副行业"
				label-width="150px"
				prop="viceIndustry"
			>
				<el-cascader
					class="industy-selection"
					clearable
					placeholder="点击选择所属副行业"
					:options="industryOptions"
					:props="industryProps"
					v-model="industryForm.viceIndustry.industryList"
				/>
			</el-form-item>
			<el-form-item
				label-width="150px"
				v-for="(item, index) in industryForm.followIndustries"
				:key="index"
			>
				<p slot="label">关注的行业{{index+1}}</p>
				<div class="item">
					<el-cascader
						class="industy-selection"
						clearable
						placeholder="点击选择关注行业"
						:options="industryOptions"
						:props="industryProps"
						v-model="item.industryList"
					/>
					<div class="delete-btn">
						<el-button
							icon="el-icon-delete"
							size="mini"
							circle
							@click="handleDeleteIndustry(index, item)"
						></el-button>
					</div>
				</div>
			</el-form-item>
			<el-form-item label-width="150px">
				<el-button
					class="add-button"
					@click="handleAddIndustry"
				>
					<i class="el-icon-circle-plus-outline"></i>增加关注行业
				</el-button>
			</el-form-item>
		</el-form>
		<div
			slot="footer"
			class="dialog-footer"
		>
			<el-button @click="handleDialogCancel">取 消</el-button>
			<el-button
				type="primary"
				:loading="submiting"
				@click="handleEditSubmit"
			>确 定</el-button>
		</div>
	</el-dialog>
</template>

<script>

import {
	FLLOW_INDUSTRY,
	DELETE_CONCER_INDUSTRY
} from '@/utils/urls';

export default {
	props: {
		visible: Boolean,
		industryOptions: {
			type: Array,
			default() {
				return [];
			}
		},
		industryDetail: {
			type: Object,
			default() {
				return {};
			}
		}
	},
	data() {
		return {
			ownerVisible: this.visible,
			industryProps: {
				value: 'id',
				label: 'type_name',
				checkStrictly: true
			},
			industryForm: this.initIndustryForm(),
			submiting: false,
			rules: {
				mainIndustry: [
					{required: true, message: '请选择企业所属主行业', trigger: 'blur'},
					{validator: this.checkMainIndustry, trigger: 'blur'}
				],
			}
		};
	},
	methods: {
		convertIndustry(industry) {
			let industryMap = {};

			if (industry.id) {
				industryMap.id = industry.id;
			}

			let industryList = [];

			if (industry.first_industry_id) {
				industryList.push(industry.first_industry_id);
				if (industry.second_industry_id) {
					industryList.push(industry.second_industry_id);
					if (industry.third_industry_id) {
						industryList.push(industry.third_industry_id);
						if (industry.fourth_industry_id) {
							industryList.push(industry.fourth_industry_id);
						}
					}
				}
			}

			industryMap.industryList = industryList;
			return industryMap;
		},
		convertIndustryParam(industryMap) {
			let industry = null;

			if (industryMap && industryMap.industryList && industryMap.industryList.length > 0) {
				let [
					first_industry_id,
					second_industry_id,
					third_industry_id,
					fourth_industry_id
				] = industryMap.industryList;

				industry = {
					first_industry_id,
					second_industry_id,
					third_industry_id,
					fourth_industry_id
				};
			}
			return industry;
		},
		initIndustryForm() {
			let mainIndustry = this.industryDetail.main ? this.convertIndustry(this.industryDetail.main) : {};

			let viceIndustry = this.industryDetail.vice ? this.convertIndustry(this.industryDetail.vice) : {};

			let followIndustries = [];

			if (this.industryDetail.follow) {
				followIndustries = this.industryDetail.follow.map(it => this.convertIndustry(it));
			}

			return {
				mainIndustry,
				viceIndustry,
				followIndustries
			};
		},
		handleDialogClose() {
			this.ownerVisible = false;
			this.$emit('update:visible', this.ownerVisible);
			this.$emit('featchIndustry');
			this.$refs['industryForm'].resetFields();
		},
		handleDialogCancel() {
			this.handleDialogClose();
		},
		// 关注行业
		handleAddIndustry() {
			if (this.industryForm.followIndustries.length >= 20) {
				this.$message.error('企业最多可关注20个行业');
			} else {
				this.industryForm.followIndustries.push([]);
			}
		},
		handleCancleConcer(id) {
			let params = {
				id
			};

			this.$axios.post(DELETE_CONCER_INDUSTRY, params)
				.then(() => {
					this.$message.success('删除成功');
					this.featchIndustry();
				}).catch(error => {
					console.log(error);
				});
		},
		handleDeleteIndustry(index, item) {
			this.industryForm.followIndustries.splice(index, 1);
			if (item.id) {
				this.handleCancleConcer(item.id);
			}
		},

		// 校验规则
		checkMainIndustry(rule, value, callback) {
			if (!value || !value.industryList || !value.industryList.length) {
				callback(new Error('请选择企业所属主行业'));
			} else {
				callback();
			}
		},
		handleEditSubmit() {
			this.$refs['industryForm'].validate((valid) => {
				if (valid) {
					this.handleEditSubmitParams();
				} else {
					console.log('error submit!!');
					return false;
				}
			});
		},
		handleEditSubmitParams() {
			let params = {};
			let mainIndustry = this.convertIndustryParam(this.industryForm.mainIndustry);

			if (mainIndustry) {
				params.main = mainIndustry;
			}

			let viceIndustry = this.convertIndustryParam(this.industryForm.viceIndustry);

			if (viceIndustry) {
				params.vice = viceIndustry;
			}


			if (this.industryForm.followIndustries && this.industryForm.followIndustries.length > 0) {
				params.follow = this.industryForm.followIndustries.map(it => this.convertIndustryParam(it)).filter(it => !!it);
			}


			this.submiting = true;
			this.$axios.post(FLLOW_INDUSTRY, params)
				.then(res => {
					this.submiting = false;
					this.handleDialogClose();
					if (this.$route.path == '/butler' || this.$route.path == '/butler/industry_concer') {
						this.$router.push(this.$route.path);
					}
					console.log(res);
				}).catch(error => {
					console.log(error);
					this.submiting = false;
					this.$message.error(error.message);
				});
		},
	},

	watch: {
		visible() {
			this.ownerVisible = this.visible;
		},
		industryDetail() {
			this.industryForm = this.initIndustryForm();
		}
	}
};
</script>
<style lang="less" scoped>
@import "~assets/css/common_avairail.less";
.editForm {
  .el-form-item {
    .item {
      display: flex;
      justify-content: space-between;
      flex: 1;
      .delete-btn {
        width: 50px;
        text-align: right;
        .el-icon-delete {
          color: #005192;
        }
      }
    }
  }
  .marginB {
    margin-bottom: 20px;
  }
  .el-cascader {
    width: 100%;
  }
  .el-input__inner {
    width: 100%;
  }
  .add-button {
    width: 100%;
    cursor: pointer;
  }
  .dialog-footer {
    text-align: center;
  }
  .explain {
    display: flex;
    flex-direction: row;
    align-items: center;
    padding: 18px 32px;
    background: @applyItemBgColor;
    border: 1px solid #bcd5e9;

    .right {
      display: flex;
      font-size: 14px;
      font-family: Microsoft YaHei;
      font-weight: 400;
      color: rgba(0, 81, 146, 1);
      .icon_tips {
        width: 39px;
        height: 39px;
        margin-right: 20px;
      }
    }
  }
}
</style>

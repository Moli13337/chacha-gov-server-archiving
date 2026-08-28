<template>
	<div>
		<empty
			v-if="empty"
			tip="本条资料订正申请已作废"
		/>
		<div
			class="declare-online-container"
			v-else
		>
			<!-- <bread-crumb/> -->
			<div class="breadcrumb-row">
				<el-divider direction="vertical"></el-divider>
				当前位置：
				<el-breadcrumb separator-class="el-icon-arrow-right">
					<el-breadcrumb-item>
						<nuxt-link to="/">首页</nuxt-link>
					</el-breadcrumb-item>
					<el-breadcrumb-item>
						<nuxt-link to="/declaration">政策申报</nuxt-link>
					</el-breadcrumb-item>
					<el-breadcrumb-item>
						<div>在线申报</div>
					</el-breadcrumb-item>
				</el-breadcrumb>
			</div>
			<div class="content">
				<h1>温江区产业扶持专项资金申请表</h1>
				<div class="explain">
					<img class="small">
					<div class="right">
						<h5>注意事项</h5>
						<p>1. 填写检查：企业填写完申报后，请点击填写检查内容是否填写完整。</p>
						<p>2. 在审核过程中发现申请单位提供材料不规范或者不足以证明有关事实，会提示您补充报送材料，请留意申报状态，及时补充报送材料。</p>
						<p>3. 项目申报表中，全部信息都为必填项，如果企业无该项信息，请填写无。</p>
						<p>4. 项目申报表中，系统会根据企业认证的信息自动填写部分内容，如果出现错误信息，请企业自行修改。并在申报后联系园区管委会进行企业信息更新。</p>
					</div>
				</div>
				<div class="link-type">
					<div class="left">
						<span>政策类型</span>
						<p>{{policyName}}</p>
					</div>
					<div class="right">
						<span>支持项目</span>
						<p style="word-break:break-all;">{{projectName}}</p>
					</div>
				</div>
				<el-tabs
					v-model="activeTabName"
					type="border-card"
				>
					<el-tab-pane
						label="企业基本情况"
						name="first"
						:disabled="!isLookMode && activeTabName !== 'first'"
					>
						<div class="form-first">

							<!-- 企业基础信息 -->
							<el-form
								class="enterprise-basic-form"
								ref="enterprise-basic-form"
								:rules="enterpriseBasicRules"
								:model="enterpriseBasicForm"
							>
								<div class="form-title">
									<div class="vertical"></div>
									<h3>企业基本情况</h3>
									<div
										class="error-tip"
										v-if="enterpriseBasicMessages && enterpriseBasicMessages.length > 0"
									>
										<img src="~assets/images/apply_error_icon.png"/>
										<template v-if="enterpriseBasicMessages.length > 3">
											<span>请完善企业基本信息！</span>
										</template>
										<template v-else>
											<span
												v-for="(item,index) in enterpriseBasicMessages"
												:key="index"
											>{{item}}</span>
										</template>
									</div>
								</div>
								<div class="form-content">
									<table class="form-table">
										<tr>
											<td class="odd">单位名称</td>
											<td class="even">
												<el-form-item
													prop="enterprise_name"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseBasicForm.enterprise_name"
														readonly
													/>
												</el-form-item>
											</td>
											<td class="odd">组织机构代码</td>
											<td class="even">
												<el-form-item
													prop="organization_code"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseBasicForm.organization_code"
														readonly
													/>
												</el-form-item>
											</td>
										</tr>
										<tr>
											<td class="odd">注册地址</td>
											<td class="even">
												<el-form-item
													prop="regist_address"
													:show-message="false"
												>
													<el-input
														autosize
														class="textarea"
														type="textarea"
														v-model="enterpriseBasicForm.regist_address"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
											<td class="odd">注册时间</td>
											<td class="even">
												<el-form-item
													prop="regist_time"
													:show-message="false"
													class="regist_time"
												>
													<el-date-picker
														class="date-picker"
														type="date"
														placeholder="选择注册日期"
														value-format="timestamp"
														v-model="enterpriseBasicForm.regist_time"
														:readonly="isLookMode || isMaterialMode"
														:editable="false"
														:picker-options="pickerOptions"
													/>
												</el-form-item>
											</td>
										</tr>
										<tr>
											<td class="odd">经营（办公）地址</td>
											<td class="even">
												<el-form-item
													prop="business_address"
													:show-message="false"
												>
													<el-input
														autosize
														class="textarea"
														type="textarea"
														v-model="enterpriseBasicForm.business_address"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
											<td class="odd">经营（办公）面积</td>
											<td class="even">
												<el-form-item
													prop="business_area"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseBasicForm.business_area"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
										</tr>
										<tr>
											<td class="odd">注册资本(万元)</td>
											<td class="even">
												<el-form-item
													prop="regist_capital"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseBasicForm.regist_capital"
														maxlength="10"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
											<td class="odd">统一社会信用代码</td>
											<td class="even">
												<el-form-item
													prop="unified_credit_code"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseBasicForm.unified_credit_code"
														readonly
													/>
												</el-form-item>
											</td>
										</tr>
										<tr>
											<td class="odd">行业类别</td>
											<td
												class="even"
												colspan="3"
											>
												<el-form-item
													prop="industry_id"
													:show-message="false"
												>
													<el-cascader
														class="industy-selection"
														clearable
														placeholder="点击选择所属行业"
														:options="industryOptions"
														:props="industryProps"
														v-model="enterpriseBasicForm.industry_id"
														:disabled="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
										</tr>
										<tr>
											<td class="odd">单位员工总数</td>
											<td class="even">
												<el-form-item
													prop="employee_number"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseBasicForm.employee_number"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
											<td class="odd">本科以上学历人数</td>
											<td class="even">
												<el-form-item
													prop="employee_degree"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseBasicForm.employee_degree"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
										</tr>
										<tr>
											<td class="odd">大专学历人数</td>
											<td class="even">
												<el-form-item
													prop="employee_junior"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseBasicForm.employee_junior"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
											<td class="odd">其他学历人数</td>
											<td class="even">
												<el-form-item
													prop="employee_other"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseBasicForm.employee_other"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
										</tr>
									</table>
								</div>
							</el-form>

							<!-- 企业通讯信息 -->
							<el-form
								class="enterprise-contact-form"
								ref="enterprise-contact-form"
								:rules="enterpriseContactRules"
								:model="enterpriseContactForm"
							>
								<div class="form-title">
									<div class="vertical"></div>
									<h3>单位联系人信息</h3>
									<div
										class="error-tip"
										v-if="enterpriseContactMessages && enterpriseContactMessages.length > 0"
									>
										<img src="~assets/images/apply_error_icon.png"/>
										<template v-if="enterpriseContactMessages.length > 3">
											<span>请完善联系人信息！</span>
										</template>
										<template v-else>
											<span
												v-for="(item,index) in enterpriseContactMessages"
												:key="index"
											>{{item}}</span>
										</template>
									</div>
								</div>
								<div class="form-content">
									<table class="form-table">
										<tr>
											<td class="odd">法人代表姓名</td>
											<td class="even">
												<el-form-item
													prop="legal_name"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseContactForm.legal_name"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
											<td class="odd odd2">手机号码</td>
											<td class="even">
												<el-form-item
													prop="legal_phone"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseContactForm.legal_phone"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
											<td class="odd odd2">微信号</td>
											<td class="even">
												<el-form-item
													prop="legal_wechat"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseContactForm.legal_wechat"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
										</tr>
										<tr>
											<td class="odd">单位负责人姓名</td>
											<td class="even">
												<el-form-item
													prop="charge_name"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseContactForm.charge_name"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
											<td class="odd odd2">手机号码</td>
											<td class="even">
												<el-form-item
													prop="charge_phone"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseContactForm.charge_phone"
														maxlength="11"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
											<td class="odd odd2">微信号</td>
											<td class="even">
												<el-form-item
													prop="charge_wechat"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseContactForm.charge_wechat"
														maxlength="20"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
										</tr>
										<tr>
											<td class="odd">联系人姓名</td>
											<td class="even">
												<el-form-item
													prop="contact_name"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseContactForm.contact_name"
														maxlength="6"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
											<td class="odd odd2">手机号码</td>
											<td class="even">
												<el-form-item
													prop="contact_phone"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseContactForm.contact_phone"
														maxlength="11"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
											<td class="odd odd2">微信号</td>
											<td class="even">
												<el-form-item
													prop="contact_wechat"
													:show-message="false"
												>
													<el-input
														type="text"
														v-model="enterpriseContactForm.contact_wechat"
														maxlength="20"
														placeholder="请填写"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</td>
										</tr>
									</table>
								</div>
							</el-form>

							<!-- 企业经济信息 -->
							<el-form
								class="enterprise-economy-form"
								ref="enterprise-economy-form"
								:rules="enterpriseEconomyRules"
								:model="{}"
							>
								<div class="form-title">
									<div class="vertical"></div>
									<h3>主要经济指标</h3>
									<div
										class="error-tip"
										v-if="enterpriseEconomyMessages && enterpriseEconomyMessages.length > 0"
									>
										<img src="~assets/images/apply_error_icon.png"/>
										<template v-if="enterpriseEconomyMessages.length > 3">
											<span>请完善经济指标！</span>
										</template>
										<template v-else>
											<span
												v-for="(item,index) in enterpriseEconomyMessages"
												:key="index"
											>{{item}}</span>
										</template>
									</div>
								</div>
								<div class="form-content">
									<el-table
										border
										style="width: 100%"
										class="economy-table"
										:data="enterpriseEconomyTableForm"
										:cell-style="handleCellColor"
										:header-cell-style="handleHeaderCellColor"
									>
										<el-table-column
											label=""
											width="180"
											align="center"
										>
											<template slot-scope="scope">
												{{ scope.row.typeName }}
											</template>
										</el-table-column>

										<el-table-column
											:label="getYear.preTowYear + '年'"
											width="276"
											align="center"
										>
											<template slot-scope="scope">
												<el-form-item
													required
													:prop="scope.row.type + '_' + getYear.preTwoYear"
												>
													<el-input
														v-model="scope.row.contentOfYears[0]"
														placeholder="请填写"
														maxlength="10"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</template>
										</el-table-column>

										<el-table-column
											:label="getYear.preYear + '年'"
											width="276"
											align="center"
										>
											<template slot-scope="scope">
												<el-form-item
													:prop="scope.row.type + '_' + getYear.preYear"
													required
												>
													<el-input
														v-model="scope.row.contentOfYears[1]"
														placeholder="请填写"
														maxlength="10"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</template>
										</el-table-column>

										<el-table-column
											:label="getYear.currentYear + '年'"
											width="276"
											align="center"
										>
											<template slot-scope="scope">
												<el-form-item
													required
													:prop="scope.row.type + '_' + getYear.currentYear"
												>
													<el-input
														v-model="scope.row.contentOfYears[2]"
														placeholder="请填写"
														maxlength="10"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</template>
										</el-table-column>
									</el-table>
								</div>
							</el-form>

						</div>
					</el-tab-pane>
					<el-tab-pane
						label="项目申报情况"
						name="second"
						:disabled="!isLookMode && activeTabName !== 'second'"
					>
						<el-form
							class="form-seconed"
							ref="declare-situation-form"
							:rules="!isLookMode && !isMaterialMode ? declareSituationRules : {}"
							:model="declareSituationForm"
						>
							<div class="form-title">
								<div class="vertical"></div>
								<h3>项目申报情况</h3>
								<div
									class="error-tip"
									v-if="declareSituationMessages && declareSituationMessages.length > 0"
								>
									<img src="~assets/images/apply_error_icon.png"/>
									<template v-if="declareSituationMessages.length > 3">
										<span>请完善项目申报信息！</span>
									</template>
									<template v-else>
										<span
											v-for="(item, index) in declareSituationMessages"
											:key="index"
										>{{item}}</span>
									</template>
								</div>
							</div>
							<div class="content-box">
								<p class="content-title">一、企业主营业务介绍</p>
								<el-form-item
									prop="business_content"
									:show-message="false"
								>
									<el-input
										type="textarea"
										placeholder="请输入内容"
										v-model="declareSituationForm.business_content"
										maxlength="1000"
										show-word-limit
										:readonly="isLookMode || isMaterialMode"
									/>
								</el-form-item>
							</div>
							<div class="content-box">
								<p class="content-title">二、项目建设（计划）主要内容（含投资、主要产品及其产能等）</p>
								<el-form-item
									prop="plan_content"
									:show-message="false"
								>
									<el-input
										type="textarea"
										placeholder="请输入内容"
										v-model="declareSituationForm.plan_content"
										maxlength="1000"
										show-word-limit
										:readonly="isLookMode || isMaterialMode"
									/>
								</el-form-item>
							</div>
							<div class="content-box">
								<p class="content-title">三、项目审批或核准、备案情况</p>
								<el-row class="form-row">
									<el-col :span="12">
										<el-row>
											<el-col :span="8">
												<p class="label">批复机关</p>
											</el-col>
											<el-col :span="16">
												<el-form-item
													prop="approval_organ"
													:show-message="false"
												>
													<el-input
														v-model="declareSituationForm.approval_organ"
														class="form-input"
														placeholder="若无相关资料请填无"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</el-col>
										</el-row>
									</el-col>
									<el-col :span="12">
										<el-row>
											<el-col :span="8">
												<p class="label">批文文号</p>
											</el-col>
											<el-col :span="16">
												<el-form-item
													prop="approval_number"
													:show-message="false"
												>
													<el-input
														v-model="declareSituationForm.approval_number"
														class="form-input"
														placeholder="若无相关资料请填无"
														:readonly="isLookMode || isMaterialMode"
													/>
												</el-form-item>
											</el-col>
										</el-row>
									</el-col>
								</el-row>
							</div>
							<div class="content-box">
								<p class="content-title">四、经认证的资格、资质、证书及称号</p>
								<el-form-item
									prop="qualifications"
									:show-message="false"
								>
									<el-input
										type="textarea"
										placeholder="若无相关资料请填无"
										v-model="declareSituationForm.qualifications"
										maxlength="1000"
										show-word-limit
										:readonly="isLookMode || isMaterialMode"
									/>
								</el-form-item>
							</div>
							<div class="content-box">
								<p class="content-title">五、申报政策条款</p>
								<el-form-item
									prop="provisions"
									:show-message="false"
								>
									<el-input
										type="textarea"
										placeholder="若无相关资料请填无"
										v-model="declareSituationForm.provisions"
										maxlength="1000"
										show-word-limit
										:readonly="isLookMode || isMaterialMode"
									/>
								</el-form-item>
								<div class="form-row-wither">
									<el-row class="form-row">
										<el-col :span="12">
											<el-row>
												<el-col :span="8">
													<p class="label">申请扶持资金计算依据（标准）</p>
												</el-col>
												<el-col :span="16">
													<el-form-item
														prop="apply_criteria"
														:show-message="false"
													>
														<el-input
															v-model="declareSituationForm.apply_criteria"
															class="form-input"
															placeholder="若无相关资料请填无"
															:readonly="isLookMode || isMaterialMode"
														/>
													</el-form-item>
												</el-col>
											</el-row>
										</el-col>
										<el-col :span="12">
											<el-row>
												<el-col :span="8">
													<div class="label">申请扶持资金金额 <p style="color: red;">单位(万元)</p></div>
												</el-col>
												<el-col :span="16">
													<el-form-item
														prop="apply_money"
														:show-message="false"
													>
														<el-input
															v-model="declareSituationForm.apply_money"
															type="number"
															class="form-input"
															placeholder="若无扶持资金金额请填 0"
															:readonly="isLookMode || isMaterialMode"
														/>
													</el-form-item>
												</el-col>
											</el-row>
										</el-col>
									</el-row>
								</div>
							</div>
							<div class="content-box">
								<p class="content-title">六、其他说明</p>
								<el-form-item
									prop="other_notes"
									:show-message="false"
								>
									<el-input
										type="textarea"
										placeholder="请输入内容"
										v-model="declareSituationForm.other_notes"
										maxlength="1000"
										show-word-limit
										:readonly="isLookMode || isMaterialMode"
									/>
								</el-form-item>
							</div>
						</el-form>
					</el-tab-pane>
					<el-tab-pane
						label="上传附件"
						name="third"
						:disabled="!isLookMode && activeTabName !== 'third'"
					>
						<div class="form-third">
							<div class="form-title">
								<div class="vertical"></div>
								<h3 class="upload-title">本次申请附件清单</h3>
							</div>
							<div class="form-table">
								<h3 class="table-title">本申请所附材料清单</h3>
								<div class="tips">
									<h3>附件清单说明:</h3>
									<p>1. 下表附件，请按照附件文件的要求，每条记录以一个文件形式上传，如果有多个文件则请在对应的附件处连续上传。</p>
									<p>2. 每个附件材料的复印件或者原件，以彩色扫描或者拍照的形式，确保足够清晰、可辨。</p>
									<p>3. 请必须提供附件列表中“是否必备材料”列标记为“是”的附件文件，标记为据实提供的材料可以企业根据实际情况进行提交，若无可不提交。</p>
									<br/>
									<h3>附件文件操作说明:</h3>
									<p>1. 系统支持JPG，JPEG，PNG，BMP, DOC，WPS，DOCX，PDF，XLS，XLSX格式的附件，单个附件要求小于100M。</p>
									<p>2. 对于大于一页的单个附件，如审计报告等，建议采用PDF格式。</p>
									<p class="red-tip">3. 请在材料名称为发票那一栏上传发票，请保证发票图片内容清晰可见，而且不要对发票编号和合计金额进行遮挡。如果是多张发票，可以进行拖拽批量上传。发票图片格式仅支持JPG、PNG、JPEG、BMP格式。</p>
								</div>
								<div class="material-table-box">
									<el-table
										class="material-table"
										border
										:data="materials"
										:header-cell-style="tableHeaderColor"
										:cell-style="tableCellColor"
									>
										<el-table-column
											align="center"
											label="序号"
											width="100px"
										>
											<template slot-scope="scope">
												<span>{{scope.$index + 1}}</span>
											</template>
										</el-table-column>
										<el-table-column
											align="center"
											prop="name"
											label="附件名称"
										>
											<template slot-scope="scope">
												<p>{{scope.row.name}}</p>
											<!-- <p v-if="scope.row.type == 2">(提示: 请上传最近一年发票)</p> -->
											</template>
										</el-table-column>
										<el-table-column
											align="center"
											prop="is_need_name"
											label="是否必备材料"
											width="120px"
										>
										</el-table-column>
										<el-table-column
											align="center"
											prop="preview"
											label="预览"
										>
											<template slot-scope="scope">
												<div
													class="upload-files"
													v-if="filteFailedfiles[scope.row.id] && filteFailedfiles[scope.row.id].length > 0"
												>
													<div class="info">
														<a
															:href="filteFailedfiles[scope.row.id][0].file_url"
															target="_blank"
														>{{filteFailedfiles[scope.row.id][0].file_name}}</a>
														<i
															v-if="filteFailedfiles[scope.row.id] && filteFailedfiles[scope.row.id].length  === 1"
															style="color: #005192"
															class="el-icon-circle-close  primaryColor"
															@click="handleLastFileRemove(scope.row.id)"
														>
														</i>
														<div
															class="list-info"
															v-if="filteFailedfiles[scope.row.id] && filteFailedfiles[scope.row.id].length > 1"
														>
															<el-popover
																placement="left"
																width="500"
																trigger="click"
															>
																<div
																	class="card"
																>
																	<div>
																		<span
																			class="title"
																			style="font-weight: 500"
																		>全部附件</span>
																	</div>
																	<div style="max-height: 400px; overflow: auto;">
																		<div
																			v-for="(item, index) in filteFailedfiles[scope.row.id]"
																			:key="index"
																			class="item"
																			style="padding: 20px; display: flex; justify-content: space-between;"
																		>
																			<p style="color: #005192; padding-right: 10px; overflow: hidden;white-space: nowrap;text-overflow:ellipsis; width: 290px;">
																				<i class="el-icon-tickets"></i>
																				<a
																					:href="item.file_url"
																					target="_blank"
																					class="file_url"
																				>{{item.file_name}}</a>
																			</p>
																			<p style="width: 180px; ">{{item.created_at | formatDate('YYYY/MM/DD HH:mm:ss')}}
																				<i
																					v-if="isCorrectMode || isCreateMode || isEdit || (isMaterialMode && scope.row.type === 0)"
																					style="color: #005192"
																					class="el-icon-circle-close primaryColor"
																					@click="handleFileRemove(scope.row.id, item.uid, index)"
																				>
																				</i>
																			</p>
																		</div>
																	</div>
																</div>
																<el-button
																	type='text'
																	slot="reference"
																>查看全部</el-button>
															</el-popover>
														</div>
													</div>
													<p class="upload-time">{{filteFailedfiles[scope.row.id][0].created_at | formatDate('YYYY/MM/DD HH:mm:ss')}}
													</p>
												</div>
												<p v-else>待上传</p>
											</template>
										</el-table-column>
										<el-table-column
											align="center"
											label="操作"
											width="120px"
											v-if="!isLookMode"
										>
											<template slot-scope="scope">
												<template v-if="isLookMode || (isMaterialMode && scope.row.type !== 0)">
													<el-button
														type="text"
													>已上传完成
													</el-button>
												</template>
												<template v-else>
													<el-button
														type="text"
														class="upload-btn"
														@click="handleUploadClick(scope.row)"
													>{{filteFailedfiles[scope.row.id] && filteFailedfiles[scope.row.id].length > 0 ? '继续上传' : '上传'}}</el-button>
												</template>
											</template>
										</el-table-column>
									</el-table>
								</div>
							</div>
							<div class="explain-container">
								<h3 class="explain-title">诚信申报承诺 <el-checkbox v-model="checked"></el-checkbox></h3>
								<div class="explain-cotent">
									<div class="content-item">
										<p>一、本单位承诺本次申报的项目真实，不存在伪造、变造、抄袭等虚假情形。</p>
										<p>二、项目建设（计划）主要内容（含投资、主要产品及其产能等），本单位承诺本次申报的项目申报资料真实、合法、有效。</p>
										<p>三、本单位承诺自申报通知发布之日起前12个月期间，未发生被三个（含）以上区级行政执法部门处罚，或同一违法行为受到区级同一行政执法部门两次（含）以上行政处罚的情形。</p>
										<p>四、本单位自愿接受主管部门、社会等对本单位的监督，获得专项资金支持后，实行专款专用，如有任何违反上述承诺的，本单位愿意承担由此产生的全部责任。</p>
									</div>

								</div>
							</div>
						</div>
					</el-tab-pane>
					<el-tab-pane
						label="审核意见"
						name="forth"
						v-if="isLookMode"
						:disabled="!isLookMode && activeTabName !== 'forth'"
					>
						<div class="step-box">
							<div  class="step">
								<!-- <el-steps
									:space="600"
									:active="isStepActive"
									finish-status="success"
									:align-center="true"
									process-status="process"
								>
									<el-step
										v-if="declareDetail.flow_status.one && declareDetail.flow_status.one.status"
										:title="declareDetail.flow_status.one.status == 1 ? '待进行' : declareDetail.flow_status.one.status == 2 ? '进行中 ' : declareDetail.flow_status.one.status == 3 ? '已完成' : '已结束'"
										description="提交申报"
									>
									</el-step>
									<el-step
										v-if="declareDetail.flow_status.two && declareDetail.flow_status.two.status"
										:title="declareDetail.flow_status.two.status == 1 ? '待进行' : declareDetail.flow_status.two.status == 2 ? '进行中 ' : declareDetail.flow_status.two.status == 3 ? '已完成' : '已结束'"
										description="区企业服务中心受理"
									>
									</el-step>
									<el-step
										v-if="declareDetail.flow_status.three && declareDetail.flow_status.three.status"
										:title="declareDetail.flow_status.three.status == 1 ? '待进行' : declareDetail.flow_status.three.status == 2 ? '进行中 ' : declareDetail.flow_status.three.status == 3 ? '已完成' : '已结束'"
										description="园区管委会审核"
									>
									</el-step>
									<el-step
										v-if="declareDetail.flow_status.four && declareDetail.flow_status.four.status"
										:title="declareDetail.flow_status.four.status == 1 ? '待进行' : declareDetail.flow_status.four.status == 2 ? '进行中 ' : declareDetail.flow_status.four.status == 3 ? '已完成' : '已结束'"
										description="管委会办公室拨款"
									>
									</el-step>
								</el-steps> -->
								<el-steps
									:active="activeStep"
									finish-status="success"
								>
									<el-step
										v-for="item in steps"
										:key="item.key"
										:title="item.title"
										:description="item.description"
									/>
								</el-steps>
							</div>
						</div>
						<div class="form-forth">
							<div class="form-title">
								<div class="vertical"></div>
								<h3 class="upload-title">各部门审核意见</h3>
							</div>
							<empty
								tip="暂无审核意见"
								v-if="!approvalList || approvalList.length === 0"
							/>
							<div
								class="approval-content"
								v-else
							>
								<div
									class="content-item"
									v-for="(item, index) in approvalList"
									:key="index"
								>
									<table v-if="item.approval_type == 1">
										<thead>
											<tr>
												<td
													class="col-head"
													colspan="2"
												>
													<span class="department">{{item.department_name}}评审意见为：</span>
												</td>
											</tr>
										</thead>
										<tbody 	v-if="declareDetail.apply_status == 4">
											<tr>
												<td
													class="col-1"
													style="padding: 10px;"
												>
													区企业服务中心意见
												</td>
												<td
													class="col-2"
													style="padding: 10px;"
												>不受理， {{item.department_mark}}</td>
											</tr>
										</tbody>
										<tbody v-else>
											<tr>
												<td
													class="col-1"
												>
													专家意见
												</td>
												<td class="col-2">
													{{item.expert_mark ? item.expert_mark : ''}}
												</td>
											</tr>
											<tr>
												<td
													class="col-1"
												>
													<span>部门评审意见</span>
												</td>
												<td class="col-2">
													{{item.department_mark?item.department_mark: '' }}
												</td>
											</tr>
										</tbody>
									</table>
									<table v-else>
										<thead>
											<tr>
												<td
													class="col-head"
													colspan="2"
												>
													<span
														class="department"
														v-if="item.approval_type === 4"
													>{{item.department_name}}会议集体决策意见：<span class="role">{{item.approval_type | approvalType }}</span></span>
													<span
														class="department"
														v-else
													>{{item.department_name}}评审意见为：<span class="role">{{item.approval_type | approvalType }}</span></span>
												</td>
											</tr>
										</thead>
										<tbody>
											<tr v-if="item.approval_type !== 4">
												<td
													class="col-1"
												>
													专家意见
												</td>
												<td class="col-2">
													{{item.expert_mark ? item.expert_mark : ''}}
												</td>
											</tr>
											<tr>
												<td
													class="col-1"
												>
													<span v-if="item.approval_type === 4">决策意见</span>
													<span v-else>部门评审意见</span>
												</td>
												<td class="col-2">
													{{item.department_mark?item.department_mark: '' }}
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<!-- 延时拨款原因 -->
								<div
									class="content-item"
									v-if="deferMark"
								>
									<table>
										<thead>
											<tr>
												<td
													class="col-head"
													colspan="2"
												>
													<span class="department">延时拨款原因</span>
												</td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td class="col-2">
													{{deferMark?deferMark:''}}
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<!-- 拨款反馈 -->
								<div
									class="content-item"
									v-if="declareDetail.support_content || declareDetail.allocation_time"
								>
									<table width="100%">
										<thead>
											<tr>
												<td
													class="col-head"
													colspan="4"
												>
													<span class="department">拨款反馈</span>
												</td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td
													class="col-1"
												>
													申报状态
												</td>
												<td
													class="col-2"
												>
													{{declareDetail.apply_status == 9 ||  declareDetail.apply_status == 8 ? '申报成功': '申报失败'}}
												</td>
												<td
													class="col-1"
												>
													获取支持
												</td>
												<td
													class="col-2"
												>
													{{declareDetail.support_content}}
												</td>
											</tr>
											<tr>
												<td
													class="col-1"
												>
													拨款状态
												</td>
												<td
													class="col-2"
												>
													{{declareDetail.apply_status == 9? '已拨款' : '待拨款'}}
												</td>
												<td
													class="col-1"
												>
													拨款时间
												</td>
												<td
													class="col-2"
												>
													{{declareDetail.allocation_time == 0 ?  '': declareDetail.allocation_time |  formatDate('YYYY/MM/DD')}}
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</el-tab-pane>

				</el-tabs>

				<div
					class="link-btn"
					v-if="isLookMode"
				>
					<div class="link-btn-right">
						<el-button
							@click="handleLookPre"
							type="primary"
							v-if="activeTabName === 'second' || activeTabName === 'third' || activeTabName === 'forth'"
						>
							上一页
						</el-button>
						<el-button
							@click="handleLookNext"
							type="primary"
							v-if="activeTabName === 'first' || activeTabName === 'second' || activeTabName === 'third'"
						>下一页</el-button>
					</div>
				</div>
				<div
					class="link-btn"
					v-else
				>
					<div class="link-btn-right">
						<el-button
							v-if="!isMaterialMode && !isCorrectMode &&  (activeTabName == 'first' || activeTabName == 'second' || activeTabName == 'third')"
							type="primary"
							@click="handleDraftSave"
							:loading="draftLoading"
						>存为草稿</el-button>
						<el-button
							@click="handlePre"
							type="primary"
							v-if="activeTabName == 'second' || activeTabName == 'third'"
						>上一页</el-button>
						<div @click="handleNext">
							<el-button
								type="primary"
								v-if="activeTabName == 'first' || activeTabName == 'second'"
								:loading="nextLoading"
							><span>下一页</span>
							</el-button>
							<el-button
								type="success"
								v-if="activeTabName == 'third'"
								:loading="submitLoading"
							>提交</el-button>
						</div>
					</div>
					<div>
						<el-button
							v-if="!isMaterialMode"
							@click="handleInputCheck"
							type="success"
						>填写检查</el-button>
					</div>
				</div>
			</div>

			<!-- 认证成功对话框 -->
			<common-dialog
				:visible.sync="dialog.visible"
				:title="dialog.title"
				:message="dialog.message"
				:buttonText="dialog.buttonText"
				:errorTip="dialog.errorTip"
			/>

			<!-- 文件上传对话框 -->
			<el-dialog
				title="上传附件"
				:visible.sync="uploadDialogVisible"
				width="500px"
				:close-on-click-modal=false
				:before-close="uploadModelClose"
			>
				<el-upload
					ref="upload"
					class="upload-container"
					drag
					multiple
					action=""
					name="file"
					:show-file-list="false"
					:data="{ business_id: businessId }"
					:http-request="customUpload"
					:beforeUpload="beforeUpload"
					:on-progress="handleUploadProgress"
					:on-change="handleUploadChange"
					:on-error="handleUploadError"
					:on-remove="handleUploadRemove"
				>
					<div slot="trigger">
						<img
							src="~assets/images/upload-icon-tip.jpg"
							class="upload-icon"
						>
						<div class="el-upload__btn">
							<el-button
								type="primary"
								@click="handleSelect"
							>{{currentMaterial && currentUploadFiles && currentUploadFiles.length > 0 ? '继续上传' : '点击选择'}}</el-button>
						</div>
						<p class="el-upload__tip">或将附件拖到这里，单次最多可选附件数量为300份</p>
					</div>
					<div
						class="upload-file-box"
						v-if="currentMaterial && currentUploadFiles && currentUploadFiles.length > 0"
					>
						<p class="box-title">已选择{{currentUploadFiles.length}}份附件，最多可选择300份附件</p>
						<ul class="upload-file-list">
							<li
								class="upload-file-item"
								v-for="(item, index) in currentUploadFiles"
								:key="index"
							>
								<img
									class="file-thumbnail"
									src="~/assets/images/icon-image.jpg"
								/>
								<div class="file-content-wrap">
									<p class="file-name">{{item.file_name}}</p>
									<span v-if="item.status === 'success'">上传完成</span>
									<span
										class="upload-fail-tip"
										v-else-if="item.status === 'fail'"
									>上传失败:{{item.errorMsg}}</span>
									<el-progress
										class="file-upload-progress"
										:percentage="item.percent"
										v-else
									/>
								</div>
								<img
									class="file-remove-btn"
									src="~assets/images/ic_close@2x.png"
									@click="handleUploadRemove(item)"
								/>
							</li>
						</ul>
						<div class="box-action-buttons">
							<el-button
								class="action-button"
								type="primary"
								@click="handleUploadConfirm"
								v-if="isUpload"
							>确定</el-button>
							<el-button
								class="action-button"
								type="default"
								@click="handleReuploadClick"
							>重新选择</el-button>
						</div>
					</div>
				</el-upload>
			</el-dialog>
			<!-- 提交成功对话框 -->
			<common-dialog
				:visible.sync="dialog.visible"
				:type="dialog.type"
				:title="dialog.title"
				:message="dialog.message"
				:errorTip="dialog.errorTip"
				:buttonText="dialog.buttonText"
				:onButtonClick="dialog.onButtonClick"
			/>
		</div>
	</div>

</template>
<script>
import axios from 'axios';
import CommonDialog from '@/components/common-dialog';
import empty from '@/components/empty';
import {
	ENTERPRISE_APPLY_DETAIL,
	DECLARATION_DETAIL,
	FEATCH_INDUSTRY,
	FETCH_DECLARE_DETAIL,
	APPLY_STORE,
	APPLY_UPDATE,
	UPLOAD_FILE,
	CORRECT_APPLY_DETAIL,
	CORRECT_UPDATE
} from '@/utils/urls.js';
import storage from '@/utils/storage.js';
const AUDIT_STATUS_MAP = {
	1: '待进行',
	2: '进行中',
	3: '已完成',
	4: '已结束'
};
const AUDIT_STEPS = [
	{
		key: 'one',
		text: '提交申报'
	},
	{
		key: 'two',
		text: '区企业服务中心受理'
	},
	{
		key: 'three',
		text: '园区管委会审核'
	},
	{
		key: 'four',
		text: '管委会办公室拨款'
	}
];

export default {
	scrollToTop: true,
	components: {
		CommonDialog,
		empty
	},
	data() {
		return {
			// 定时器
			empty: false,
			stepActive: 2,
			errorPrompt: true,
			saveTimer: null,
			submitLoading: false,
			draftLoading: false,
			nextLoading: false,
			checked: false,
			activeTabName: 'first',
			industryProps: {
				value: 'id',
				label: 'type_name',
				checkStrictly: true
			},
			// 从后台获取到的企业数据，项目申报数据
			industryOptions: [],
			enterpriseInfo: {},
			declarationInfo: {},
			// 数据保存的ID
			nextStepId: 0,
			// 草稿保存的ID
			draftId: 0,
			childrenID: 0,
			noAcceptedID: 0,
			// 企业基本信息版块表单相关
			enterpriseBasicMessages: [],
			enterpriseBasicForm: {},
			enterpriseBasicRules: {
				// 单位名称
				enterprise_name: [
					{required: true, message: '请填写单位名称!', trigger: 'blur'},
					{max: 40, message: '单位名称只能为小于等于40的字符！', trigger: 'blur'}
				],
				// 组织机构代码
				organization_code: [
					{required: true, message: '请填写组织机构代码！', trigger: 'blur'},
					{max: 100, message: '组织机构代码只能为小于等于100的字符！', trigger: 'blur'}
				],
				// 注册地址
				regist_address: [
					{required: true, message: '请填写注册地址！', trigger: 'blur'},
					{max: 100, message: '注册地址只能为小于等于100的字符！', trigger: 'blur'}
				],
				// 注册时间
				regist_time: [
					{required: true, message: '请填写注册时间！', trigger: 'blur'}
				],
				// 经营（办公）地址
				business_address: [
					{required: true, message: '请填写经营（办公）地址！', trigger: 'blur'},
					{max: 100, message: '经营（办公）地址只能为小于等于100的字符！', trigger: 'blur'}
				],
				// 经营（办公）面积
				business_area: [
					{required: true, message: '请填写经营（办公）面积！', trigger: 'blur'},
					{max: 10, message: '经营（办公）面积只能为小于等于10的字符！', trigger: 'blur'}
				],
				// 注册资本
				regist_capital: [
					{required: true, message: '请填写注册资本！', trigger: 'blur'},
					{max: 10, message: '注册资本只能为小于等于10的字符！', trigger: 'blur'}
				],
				// 统一信用代码
				unified_credit_code: [
					{required: true, message: '请填写统一信用代码！', trigger: 'blur'},
					{max: 20, message: '统一信用代码只能为小于等于20的字符！', trigger: 'blur'}
				],
				// 行业类别
				industry_id: [
					{required: true, message: '请填写行业类别！', trigger: 'blur'}
				],
				// 单位员工总数
				employee_number: [
					{required: true, message: '请填写单位员工总数！', trigger: 'blur'},
					{max: 6, message: '单位员工总数只能为小于等于6的字符！', trigger: 'blur'}
				],
				// 本科以上学历人数
				employee_degree: [
					{required: true, message: '请填写本科以上学历人数！', trigger: 'blur'},
					{max: 6, message: '本科以上学历人数只能为小于等于6的字符！', trigger: 'blur'}
				],
				// 大专学历人数
				employee_junior: [
					{required: true, message: '请填写大专学历人数！', trigger: 'blur'},
					{max: 6, message: '大专学历人数只能为小于等于6的字符！', trigger: 'blur'}
				],
				// 其他学历人数
				employee_other: [
					{required: true, message: '请填写其他学历人数！', trigger: 'blur'},
					{max: 6, message: '学历人数只能为小于等于6的字符！', trigger: 'blur'}
				],
			},
			// 企业通讯版块表单相关
			enterpriseContactMessages: [],
			enterpriseContactForm: {},
			enterpriseContactRules: {
				// 法定代表人
				legal_name: [
					{required: true, message: '请填写法定代表人！', trigger: 'blur'},
					{max: 6, message: '法人姓名字符长度不超过6！', trigger: 'blur'}
				],
				// 法人手机号
				legal_phone: [
					{required: true, message: '请填写法人手机号！', trigger: 'blur'},
					{max: 11, message: '手机号字符长度不超过11！', trigger: 'blur'}
				],
				// 法人微信号
				legal_wechat: [
					{required: true, message: '请填写法人微信号！', trigger: 'blur'},
					{max: 20, message: '微信号字符长度不超过20！', trigger: 'blur'}
				],
				// 单位负责人姓名
				charge_name: [
					{required: true, message: '请填写单位负责人姓名！', trigger: 'blur'},
					{max: 6, message: '单位负责人姓名字符长度不超过6！', trigger: 'blur'}
				],
				// 负责人手机号
				charge_phone: [
					{required: true, message: '请填写负责人手机号！', trigger: 'blur'},
					{max: 11, message: '负责人手机号字符长度不超过11！', trigger: 'blur'}
				],
				// 负责人微信
				charge_wechat: [
					{required: true, message: '请填写负责人微信！', trigger: 'blur'},
					{max: 20, message: '负责人微信字符长度不超过20！', trigger: 'blur'}
				],
				// 联系人姓名
				contact_name: [
					{required: true, message: '请填写联系人姓名！', trigger: 'blur'},
					{max: 6, message: '联系人姓名字符长度不超过6！', trigger: 'blur'}
				],
				// 联系人手机号
				contact_phone: [
					{required: true, message: '请填写联系人手机号！', trigger: 'blur'},
					{max: 11, message: '联系人手机号字符长度不超过11！', trigger: 'blur'}
				],
				// 联系人微信
				contact_wechat: [
					{required: true, message: '请填写联系人微信！', trigger: 'blur'},
					{max: 20, message: '联系人微信字符长度不超过20！', trigger: 'blur'}
				],
			},
			// 企业经济指标版本表单相关
			enterpriseEconomyMessages: [],
			enterpriseEconomyTableForm: [
				{
					type: 1,
					typeName: '销售收入(万元)',
					contentOfYears: ['', '', '']
				},
				{
					type: 2,
					typeName: '总产值(万元)',
					contentOfYears: ['', '', '']
				},
				{
					type: 3,
					typeName: '营业收入(万元)',
					contentOfYears: ['', '', '']
				},
				{
					type: 4,
					typeName: '主营业务收入(万元)',
					contentOfYears: ['', '', '']
				},
				{
					type: 5,
					typeName: '净利润(万元)',
					contentOfYears: ['', '', '']
				},
				{
					type: 6,
					typeName: '出口总额(万元)',
					contentOfYears: ['', '', '']
				},
				{
					type: 7,
					typeName: '纳税额(万元)',
					contentOfYears: ['', '', '']
				}
			],
			enterpriseEconomyRules: {

			},
			// 项目申报情况表单相关
			declareSituationMessages: [],
			declareSituationForm: {},
			declareSituationRules: {
				business_content: [
					{required: true, message: '企业主营业务介绍不能为空！'},
				],
				plan_content: [
					{required: true, message: '项目建设主要内容不能为空！'},
				],
				approval_organ: [
					{required: true, message: '批复机关不能为空！'},
					{max: 100, message: '批复机关最大字符长度为100！', trigger: 'blur'}
				],
				approval_number: [
					{required: true, message: '批文文号不能为空！'},
					{max: 100, message: '批文文号最大字符长度为100！', trigger: 'blur'}
				],
				qualifications: [
					{required: true, message: '经认证的资格、资质、证书及称号不能为空！'},
				],
				provisions: [
					{required: true, message: '申报政策条款不能为空！'},
				],
				apply_criteria: [
					{required: true, message: '申请扶持资金计算依据（标准）不能为空！'},
					{max: 500, message: '申请扶持资金计算依据最大字符长度为500！', trigger: 'blur'}
				],
				apply_money: [
					{required: true, message: '申请扶持资金金额不能为空！'},
					{max: 20, message: '申请扶持资金最大字符长度为20！', trigger: 'blur'}
				],
				other_notes: [
					{required: true, message: '其他说明不能为空！'},
				]
			},
			// 附件材料表单相关
			uploadDialogVisible: false,
			currentMaterial: null,
			currentUploadFiles: [],
			uploadedMaterialMap: {},
			dialog: {
				visible: false,
			},
			pickerOptions: {
				disabledDate(time) {
					let timeDate = '1970-01-02 00:00:00';
					let Time = new Date(timeDate);
					let timestemp = Time.getTime();

					return time.getTime() < timestemp;
				}
			}
		};
	},
	// 请求企业的数据、申报项目的数据以及所有的行业数据
	async asyncData({query, params, $axios}) {
		if (params.mode === 'look' || params.mode === 'edit' || params.mode === 'material' || params.mode === 'correct') {
			let requestParams = {id: query.id};

			if (params.mode === 'look') {
				requestParams.has_approval = query.need_approval;
			}

			return Promise.all([
				$axios.get(params.mode === 'correct' ? CORRECT_APPLY_DETAIL : FETCH_DECLARE_DETAIL, {params: requestParams}),
				$axios.get(FEATCH_INDUSTRY)
			])
				.then(([declareDetail, industryOptions]) => ({
					declareDetail,
					industryOptions,
					draftId: query && query.id || 0
				}))
				.catch(e => {
					console.log('222', e.code);
					if (e.code == 23011) {
						return {
							empty: true
						};
					}
				});
		} else {
			return Promise.all([
				$axios.get(ENTERPRISE_APPLY_DETAIL),
				$axios.get(FEATCH_INDUSTRY),
				$axios.get(DECLARATION_DETAIL, {params: {id: query.id}}),
			])
				.then(([enterpriseInfo, industryOptions, declarationInfo]) => ({
					enterpriseInfo,
					declarationInfo,
					industryOptions,
					draftId: query && query.draftId || 0,
				}))
				.catch(e => {
					console.log(e);
				});
		}
	},
	computed: {
		flowStatus() {
			return this.declareDetail && this.declareDetail.flow_status;
		},
		activeStep() {
			let activeStep = 0;

			if (this.flowStatus) {
				AUDIT_STEPS.forEach((it, index) => {
					let found = this.flowStatus[it.key];

					if (found && found.status === 3) {
						// 加1是因为element的active是从1开始的
						activeStep = (index + 1);
					}
				});
			}
			return activeStep;
		},
		steps() {
			let steps = [];

			if (this.flowStatus) {
				AUDIT_STEPS.forEach(it => {
					let found = this.flowStatus[it.key];

					if (found) {
						let step = {
							title: AUDIT_STATUS_MAP[found.status],
							description: it.text
						};

						steps.push(step);
					}
				});
			}
			return steps;
		},
		// 判断文件上传完成
		isUpload() {
			if (!this.currentMaterial) {
				return true;
			}

			let isAllUploadIdle = true;

			if (this.currentUploadFiles) {
				this.currentUploadFiles.forEach(file => {
					if (file.status !== 'success' && file.status !== 'fail') {
						isAllUploadIdle = false;
					}
				});
			}

			return isAllUploadIdle;
		},
		// id生成规则
		businessId() {
			let date = new Date().getTime();

			return `wenjiang-${date}-${date.toString().substr(-5)}`;
		},
		// 获取经济表年份
		getYear() {
			let date = new Date();
			let currentYear = date.getFullYear();

			return {
				preTowYear: currentYear - 3 + '',
				preYear: currentYear - 2 + '',
				currentYear: currentYear - 1 + ''
			};
		},
		// 是否是查看模式
		isLookMode() {
			return this.$route.params && this.$route.params.mode === 'look';
		},
		// 是否是补充材料模式
		isMaterialMode() {
			return this.$route.params && this.$route.params.mode === 'material';
		},
		// 是否是新增默认
		isCreateMode() {
			return this.$route.params && this.$route.params.mode === 'create';
		},
		// 是否是编辑模式
		isEdit() {
			return this.$route.params && this.$route.params.mode === 'edit';
		},
		// 是否是订正模式
		isCorrectMode() {
			return this.$route.params && this.$route.params.mode === 'correct';
		},
		// 政策类型
		policyName() {
			return (this.declarationInfo && this.declarationInfo.mold_name) || (this.declareDetail && this.declareDetail.policy_name);
		},
		// 支持项目
		projectName() {
			return (this.declarationInfo && this.declarationInfo.name) || (this.declareDetail && this.declareDetail.project_name);
		},
		// 附件
		materials() {
			let materials = (this.declarationInfo && this.declarationInfo.materials) || (this.declareDetail && this.declareDetail.config);

			if (this.isLookMode || this.isCorrectMode) {
				// 是否有补充材料，true返回materials，false判断是否有附件存在，true即显示
				materials = materials && materials.filter(item => item.type !== 0 || (item.file_list && item.file_list.length > 0));
			} else if (!this.isMaterialMode) {
				// 是否有补充材料
				materials = materials && materials.filter(item => item.type !== 0);
			}

			return materials;
		},
		// 审查意见
		approvalList() {
			return this.declareDetail.approval_list;
		},
		// 反馈意见
		deferMark() {
			return this.declareDetail.defer_mark;
		},
		// 过滤失败文件
		filteFailedfiles() {
			let materialListMap = {};

			if (this.uploadedMaterialMap) {
				for (let key in this.uploadedMaterialMap) {
					let files = this.uploadedMaterialMap[key];

					if (files && files.length > 0) {
						materialListMap[key] = files && files.filter(file => !file.status || file.status === 'success');
					}
				}
			}
			return materialListMap;
		}
	},
	filters: {
		// 判断部门类型
		approvalType(type) {
			switch (type) {
				case 1:
					return '企业服务部';
				case 2:
					return '主审部门';
				case 3:
					return '协同部门';
				case 4:
					return '指挥部';
				case 5:
					return '园区办公室';
				default:
					return '';
			}
		},
		filters: {
			filtersStatus: function (status) {
				let statusName = '';

				switch (status) {
					case 1:
						statusName = '待进行';
						break;
					case 2:
						statusName = '进行中';
						break;
					case 3:
						statusName = '已完成';
						break;
					case 4:
						statusName = '已结束';
						break;
					default:
						break;
				}
				return statusName;
			},
			filtersDepartment: function (flow_status) {
				let department = '';

				switch (flow_status) {
					case 'one':
						department = '提交申报';
						break;
					case 'two':
						department = '区企业服务中心受理';
						break;
					case 'three':
						department = '园区管委会审核';
						break;
					case 'four':
						department = '管委会办公室拨款';
						break;
					default:
						break;
				}
				return department;
			},
		}
	},
	methods: {
		// 开始间隔保存草稿到本地
		startIntervalSave() {
			// 只有新建模式才离线保存
			if (this.isCreateMode) {
				this.saveTimer = setInterval(() => {
					// 以id维度进行保存
					let saveData = storage.getItem('saveData') || {};

					saveData[this.$route.query.id] = {
						enterpriseBasicForm: this.enterpriseBasicForm,
						enterpriseContactForm: this.enterpriseContactForm,
						enterpriseEconomyTableForm: this.enterpriseEconomyTableForm,
						declareSituationForm: this.declareSituationForm
					};
					storage.setItem('saveData', saveData);
				}, 2000);
			}
		},
		// 停止间隔保存草稿到本地
		stopIntervalSave() {
			if (this.saveTimer) {
				clearInterval(this.saveTimer);
			}
		},
		// 清除本地保存的草稿
		clearIntervalSave() {
			// 只有新建模式才有离线保存
			if (this.isCreateMode) {
				// 先停止定时保存
				this.stopIntervalSave();

				let saveData = storage.getItem('saveData') || {};

				// 以id维度进行保存
				delete saveData[this.$route.query.id];
				// 重新保存以删除当前id对应的本地草稿
				storage.setItem('saveData', saveData);
			}
		},
		// 弹窗关闭事件
		uploadModelClose() {
			this.$confirm('点击关闭按钮文件不会上传, 确认关闭？')
				.then(() => {
					this.currentUploadFiles = [];
					this.currentMaterial = null;
					this.uploadDialogVisible = false;
				})
				.catch(error => {
					console.log(error);
				});
		},
		// 初始化行业级联选择
		initindustrySelect() {
			let industrySelect = [];

			if (this.enterpriseInfo.industry) {
				if (this.enterpriseInfo.industry.first_industry_id) {
					industrySelect.push(this.enterpriseInfo.industry.first_industry_id);
				}
				if (this.enterpriseInfo.industry.second_industry_id) {
					industrySelect.push(this.enterpriseInfo.industry.second_industry_id);
				}
				if (this.enterpriseInfo.industry.third_industry_id) {
					industrySelect.push(this.enterpriseInfo.industry.third_industry_id);
				}
				if (this.enterpriseInfo.industry.fourth_industry_id) {
					industrySelect.push(this.enterpriseInfo.industry.fourth_industry_id);
				}
			}
			return industrySelect;
		},
		// 初始化企业经济情况的表单数据
		initEnterpriseEconomyForm() {
			let economy_list;

			if (this.declareDetail) {
				economy_list = this.declareDetail.economy_list || [];
			} else {
				economy_list = this.enterpriseInfo && this.enterpriseInfo.economy_list || [];
			}

			let temp = this.enterpriseEconomyTableForm.map(item => {
				item.contentOfYears = economy_list.map(item1 => {
					let contents = item1.content_list && item1.content_list.filter(contentItem => contentItem.type === item.type).map(item3 => item3.content);

					return contents && contents[0] || '';
				});
				return item;
			});

			return temp;
		},
		// 初始化企业相关信息的表单数据
		initEnterpriseForms() {
			if (this.declareDetail) {
				// 不存在时需要返回空字符串（element组件限制），
				let handledRegistTime = this.declareDetail.regist_time || '';

				if (handledRegistTime && handledRegistTime.toString().length === 10) {
					handledRegistTime += '000';
				}

				// 企业基础信息
				this.enterpriseBasicForm = {
					title: '温江区产业扶持专项资金申请表',
					project_id: this.declareDetail.project_id,
					project_name: this.declareDetail.project_name,
					policy_name: this.declareDetail.policy_name,
					mold_id: this.declareDetail.mold_id,
					config: this.declareDetail.config,
					enterprise_id: this.declareDetail.enterprise_id,
					enterprise_name: this.declareDetail.enterprise_name,
					unified_credit_code: this.declareDetail.unified_credit_code,
					organization_code: this.declareDetail.organization_code,
					regist_capital: this.declareDetail.regist_capital,
					regist_address: this.declareDetail.regist_address,
					// 设置为处理过的时间
					regist_time: handledRegistTime,
					business_area: this.declareDetail.business_area,
					business_address: this.declareDetail.business_address,
					employee_number: this.declareDetail.employee_number,
					employee_degree: this.declareDetail.employee_degree,
					employee_junior: this.declareDetail.employee_junior,
					employee_other: this.declareDetail.employee_other,
					// 详情接口的行业id是字符串类型的，而行业列表的id是数字
					industry_id: this.declareDetail.industry_id.map(item => Number(item))
				};
				// 企业联系人信息
				this.enterpriseContactForm = {
					legal_name: this.declareDetail.legal_name,
					legal_wechat: this.declareDetail.legal_wechat,
					legal_phone: this.declareDetail.legal_phone,

					contact_wechat: this.declareDetail.contact_wechat,
					contact_phone: this.declareDetail.contact_phone,
					contact_name: this.declareDetail.contact_name,

					charge_wechat: this.declareDetail.charge_wechat,
					charge_phone: this.declareDetail.charge_phone,
					charge_name: this.declareDetail.charge_name

				};
				// 企业经济情况
				this.enterpriseEconomyTableForm = this.initEnterpriseEconomyForm();
			} else {
				// 从本地恢复填写数据
				let saveData = storage.getItem('saveData') || {};
				let localData = saveData[this.$route.query.id];

				// 不存在时需要返回空字符串（element组件限制），
				let handledRegistTime = this.enterpriseInfo.regist_time || '';

				if (handledRegistTime && handledRegistTime.toString().length === 10) {
					handledRegistTime += '000';
				}
				// 企业基础信息
				this.enterpriseBasicForm = Object.assign({
					title: '温江区产业扶持专项资金申请表',
					project_id: this.declarationInfo.project_id,
					project_name: this.declarationInfo.name,
					mold_id: this.declarationInfo.mold_id,
					policy_name: this.declarationInfo.mold_name,
					config: this.declarationInfo.materials,
					enterprise_id: this.enterpriseInfo.id,
					enterprise_name: this.enterpriseInfo.name,
					unified_credit_code: this.enterpriseInfo.unified_credit_code,
					organization_code: this.enterpriseInfo.organization_code,
					regist_capital: this.enterpriseInfo.regist_capital,
					regist_address: this.enterpriseInfo.regist_address,
					// 设置为处理过的时间
					regist_time: handledRegistTime,
					business_area: this.enterpriseInfo.business_area,
					business_address: this.enterpriseInfo.business_address,
					employee_number: this.enterpriseInfo.employee_number,
					employee_degree: this.enterpriseInfo.employee_degree,
					employee_junior: this.enterpriseInfo.employee_junior,
					employee_other: this.enterpriseInfo.employee_other,
					industry_id: this.initindustrySelect()
				}, localData ? localData.enterpriseBasicForm : {});
				// 企业联系人信息
				this.enterpriseContactForm = Object.assign({
					legal_name: this.enterpriseInfo.legal_name,
					legal_phone: this.enterpriseInfo.legal_phone,
					legal_wechat: this.enterpriseInfo.legal_wechat,
					charge_wechat: this.enterpriseInfo.charge_wechat,
					contact_name: this.enterpriseInfo.contact_name,
					contact_phone: this.enterpriseInfo.contact_phone,
					contact_wechat: this.enterpriseInfo.contact_wechat,
					charge_name: this.enterpriseInfo.charge_name,
					charge_phone: this.enterpriseInfo.charge_phone,
				}, localData ? localData.enterpriseContactForm : {});
				// 企业经济情况
				this.enterpriseEconomyTableForm = localData ? localData.enterpriseEconomyTableForm : this.initEnterpriseEconomyForm();
			}
		},
		// 初始化项目申报情况的表单数据
		initDeclareSituationForm() {
			if (this.declareDetail) {
				this.declareSituationForm = {
					other_notes: this.declareDetail.other_notes,
					apply_money: this.declareDetail.apply_money || 0,
					apply_criteria: this.declareDetail.apply_criteria,
					provisions: this.declareDetail.provisions,
					qualifications: this.declareDetail.qualifications,
					approval_number: this.declareDetail.approval_number,
					approval_organ: this.declareDetail.approval_organ,
					plan_content: this.declareDetail.plan_content,
					business_content: this.declareDetail.business_content,
				};
			} else {
				// 从本地恢复填写数据
				let saveData = storage.getItem('saveData') || {};
				let localData = saveData[this.$route.query.id];

				this.declareSituationForm = Object.assign({
					other_notes: this.enterpriseInfo.other_notes,
					apply_money: this.enterpriseInfo.apply_money || 0,
					apply_criteria: this.enterpriseInfo.apply_criteria,
					provisions: this.enterpriseInfo.provisions,
					qualifications: this.enterpriseInfo.qualifications,
					approval_number: this.enterpriseInfo.approval_number,
					approval_organ: this.enterpriseInfo.approval_organ,
					plan_content: this.enterpriseInfo.plan_content,
					business_content: this.enterpriseInfo.business_content,
				}, localData ? localData.declareSituationForm : {});
			}
		},
		// // 初始化材料上传的表单数据
		// initMaterialUploadForm() {
		// 	if (this.declareDetail && (this.$route.query.apply_status && this.$route.query.apply_status != 1)) {
		// 		let uploadedMaterialMap = {};

		// 		this.declareDetail.config && this.declareDetail.config.forEach(item => {
		// 			uploadedMaterialMap[item.id] = item.file_list;
		// 		});

		// 		this.uploadedMaterialMap = uploadedMaterialMap;
		// 	}
		// },
		// 初始化材料上传的表单数据
		initMaterialUploadForm() {
			if (this.declareDetail) {
				let uploadedMaterialMap = {};

				this.declareDetail.config && this.declareDetail.config.forEach(item => {
					if (item && item.file_list) {
						uploadedMaterialMap[item.id] = item.file_list.map(file => ({
							...file,
							status: 'success'
						}));
					}
				});

				this.uploadedMaterialMap = uploadedMaterialMap;
			}
		},
		// 检查企业基本信息的填写
		checkEnterpriseBasicInput() {
			return new Promise((resolve, reject) => {
				// 检查前先把之前的错误消息重置
				this.enterpriseBasicMessages = [];
				// 校验企业基本信息
				this.$refs['enterprise-basic-form'].validate((valid, invalidFields) => {
					if (valid) {
						resolve();
					} else {
						let messages = [];

						Object.values(invalidFields).forEach(items => {
							items.forEach(item => {
								messages.push(item.message);
							});
						});
						this.enterpriseBasicMessages = messages;
						reject(messages);
					}
				});
			});
		},
		// 检查企业联系人信息的填写
		checkEnterpriseContactInput() {
			return new Promise((resolve, reject) => {
				// 检查前先把之前的错误消息重置
				this.enterpriseContactMessages = [];
				// 校验企业联系人信息
				this.$refs['enterprise-contact-form'].validate((valid, invalidFields) => {
					if (valid) {
						resolve();
					} else {
						let messages = [];

						Object.values(invalidFields).forEach(items => {
							items.forEach(item => {
								messages.push(item.message);
							});
						});
						this.enterpriseContactMessages = messages;
						reject(messages);
					}
				});
			});
		},
		// 检查企业经济情况的填写
		checkEnterpriseEconomyInput() {
			return new Promise((resolve, reject) => {
				// 检查前先把之前的错误消息重置
				this.enterpriseEconomyMessages = [];

				let messages = [];

				this.enterpriseEconomyTableForm.forEach(item => {
					if (!item.contentOfYears || !item.contentOfYears.length || item.contentOfYears.length != 3) {
						messages.push('请填写' + item.typeName + '！');
					} else {
						let haveEmpty = false;

						item.contentOfYears.forEach(content => {
							if (!content) {
								haveEmpty = true;
							}
						});
						if (haveEmpty) {
							messages.push('请填写' + item.typeName + '！');
						}
					}
				});

				if (!messages.length) {
					resolve();
				} else {
					this.enterpriseEconomyMessages = messages;
					reject(messages);
				}
			});
		},
		// 检查项目申报情况的填写
		checkDeclareInfoInput() {
			return new Promise((resolve, reject) => {
				this.declareSituationMessages = [];
				this.$refs['declare-situation-form'].validate((valid, invalidFields) => {
					if (valid) {
						resolve();
					} else {
						let messages = [];

						Object.values(invalidFields).forEach(items => {
							items.forEach(item => {
								messages.push(item.message);
							});
						});
						this.declareSituationMessages = messages;
						reject(messages);
					}
				});
			});
		},
		// 检查附件材料的上传情况
		checkMaterialUpload() {
			return new Promise((resolve, reject) => {
				let notUploadMaterials = [];

				this.materials.forEach(material => {
					// 检查必须上传的文件是否存在
					if (this.isMaterialMode && material.type === 0) {
						if (!this.uploadedMaterialMap[material.id] || !this.uploadedMaterialMap[material.id].length) {
							notUploadMaterials.push(material);
						}
					} else {
						if (material.is_need === 1 && (!this.uploadedMaterialMap[material.id] || !this.uploadedMaterialMap[material.id].length)) {
							notUploadMaterials.push(material);
						}
					}
				});
				if (!notUploadMaterials.length) {
					resolve();
				} else {
					reject(notUploadMaterials);
				}
			});
		},
		// 填写检查
		handleInputCheck() {
			if (this.activeTabName == 'first') {
				Promise.all([
					this.checkEnterpriseBasicInput(),
					this.checkEnterpriseContactInput(),
					this.checkEnterpriseEconomyInput()
				])
					.then(() => {
						this.$message.success('本页信息填写完成');
					})
					.catch(() => {
						this.$message.error('本页信息填写不完整，请检查');
					});
			} else if (this.activeTabName == 'second') {
				this.checkDeclareInfoInput()
					.then(() => {
						this.$message.success('本页信息填写完成');
					})
					.catch(() => {
						this.$message.error('本页信息填写不完整，请检查');
					});
			} else if (this.activeTabName == 'third') {
				this.checkMaterialUpload()
					.then(() => {
						this.$message.success('本页信息填写完成');
					})
					.catch(() => {
						this.$message.error('必传的材料未上传，请检查');
					});
			}
		},
		// 存为草稿
		handleDraftSave() {
			// 第一步：企业信息
			if (this.activeTabName == 'first') {
				// 详情接口返回给的是数字，提交时却要字符串，鄙视
				let handledRegistTime = this.enterpriseBasicForm.regist_time + '';

				// 后端需要的时间戳是10位
				if (handledRegistTime.length >= 10) {
					handledRegistTime = handledRegistTime.slice(0, -3);
				}
				let params = {
					save_type: 1,
					...this.enterpriseBasicForm,
					...this.enterpriseContactForm,
					economy_list: this.assembleEconomyList(),
					// 重写enterpriseBasicForm里的regist_time值
					regist_time: handledRegistTime
				};

				this.handleDraftInternal(params);
				this.draftLoading = true;
			} else if (this.activeTabName == 'second') {
				// 第二步：项目申报情况
				let params = {
					save_type: 1,
					...this.declareSituationForm,
				};

				this.handleDraftInternal(params);
				this.draftLoading = true;
			} else {
				let params = {
					save_type: 1,
					file_list: this.assembleMaterialList()
				};

				this.handleDraftInternal(params);
				this.draftLoading = true;
			}
		},
		// 内部的处理草稿保存
		handleDraftInternal(params) {
			// 草稿的save_type为1
			params.save_type = 1;

			// 不受理保存草稿模式
			if (this.isEdit && this.$route.query.apply_status == 4) {
				if (this.childrenID) {
					params.id = this.childrenID;
					delete params.config;
					this.$axios.post(APPLY_UPDATE, params)
						.then(() => {
							this.$message.success('草稿保存成功，可到个人中心查看');
							this.draftLoading = false;
						})
						.catch(({code, message}) => {
							if (code == 901) {
								this.$message.error('您的账号已退出登录可能在其它设备登录，请重新登录');
								// this.$router.push('/login');
								this.tencentLogin();
							} else {
								this.$message.error('草稿保存失败,' + message);
								this.draftLoading = false;
							}
						});
				} else {
					params.children_id = this.noAcceptedID;
					this.$axios.post(APPLY_STORE, params)
						.then(id => {
							this.childrenID = id;
							if (id) {
								this.$message.success('草稿保存成功，可到个人中心查看');
								this.draftLoading = false;
							}
						})
						.catch(({code, message}) => {
							if (code == 13008) {
								console.log(code);
							} else {
								this.$message.error('草稿保存失败,' + message);
								this.draftLoading = false;
							}
						});
				}
			} else {
				// 是否已有草稿，有草稿则为草稿修改操作
				if (this.draftId) {
					params.id = this.draftId;
					delete params.config;
					this.$axios.post(APPLY_UPDATE, params)
						.then(() => {
							this.$message.success('草稿保存成功，可到个人中心查看');
							this.draftLoading = false;
						})
						.catch(({code, message}) => {
							if (code == 901) {
								this.$message.error('您的账号已退出登录可能在其它设备登录，请重新登录');
								// this.$router.push('/login');
								this.tencentLogin();
							} else {
								this.$message.error('草稿保存失败,' + message);
								this.draftLoading = false;
							}
						});
				} else {
					this.$axios.post(APPLY_STORE, params)
						.then(id => {
							this.draftId = id;
							// 将草稿id存储到url上
							this.$router.replace({query: {
								...this.$route.query,
								draftId: id
							}});

							this.$message.success('草稿保存成功，可到个人中心查看');
							this.draftLoading = false;
						})
						.catch(({code, message}) => {
							if (code == 13008) {
								console.log(code);
							} else {
								this.$message.error('草稿保存失败,' + message);
								this.draftLoading = false;
							}
						});
				}
			}
		},
		// 企业相关信息保存
		handleEnterpriceInfoSave() {
			// 详情接口返回给的是数字，提交时却要字符串，鄙视
			let handledRegistTime = this.enterpriseBasicForm.regist_time + '';

			// 后端需要的时间戳是10位
			if (handledRegistTime.length > 10) {
				handledRegistTime = handledRegistTime.slice(0, -3);
			}

			let params = {
				save_type: 2,
				...this.enterpriseBasicForm,
				...this.enterpriseContactForm,
				economy_list: this.assembleEconomyList(),
				// 覆盖enterpriseBasicForm的注册时间值
				regist_time: handledRegistTime
			};

			// 编辑不受理模式
			if (this.isEdit && this.$route.query.apply_status == 4) {
				if (!this.childrenID) {
					params.children_id = this.noAcceptedID;
					this.$axios.post(APPLY_STORE, params)
						.then((res) => {
							console.log('企业信息', res);
							this.childrenID = res;
							this.activeTabName = 'second';
							this.$message.success('保存企业信息成功');
							this.nextLoading = false;
						})
						.catch(({message}) => {
							this.$message.error('保存企业信息失败,' + message);
							this.nextLoading = false;
						});
				} else {
					params.id = this.childrenID;
					delete params.config;
					this.$axios.post(APPLY_UPDATE, params)
						.then(() => {
							this.activeTabName = 'second';
							this.$message.success('保存企业信息成功');
							this.nextLoading = false;
						})
						.catch(({message}) => {
							this.$message.error('保存企业信息失败,' + message);
							this.nextLoading = false;
						});
				}
				return;
			}
			if (!this.draftId) {
				// 新建草稿模式
				this.$axios.post(APPLY_STORE, params)
					.then((res) => {
						this.draftId = res;
						this.activeTabName = 'second';
						this.$message.success('保存企业信息成功');
						this.nextLoading = false;
					})
					.catch(({message}) => {
						this.$message.error('保存企业信息失败,' + message);
						this.nextLoading = false;
					});
			} else {
				params.id = this.draftId;
				delete params.config;
				this.$axios.post(this.isCorrectMode ? CORRECT_UPDATE : APPLY_UPDATE, params)
					.then(() => {
						this.activeTabName = 'second';
						this.$message.success('保存企业信息成功');
						this.nextLoading = false;
					})
					.catch(({message}) => {
						this.$message.error('保存企业信息失败,' + message);
						this.nextLoading = false;
					});
			}
		},
		// 项目申报情况保存
		handleDeclareSituationSave() {
			let params = {
				save_type: 3,
				id: this.draftId,
				...this.declareSituationForm
			};

			if (this.isEdit && this.$route.query.apply_status == 4) {
				params.id = this.childrenID;
			}
			this.$axios.post(this.isCorrectMode ? CORRECT_UPDATE : APPLY_UPDATE, params)
				.then(() => {
					this.activeTabName = 'third';
					this.$message.success('保存项目申报情况成功');
					this.nextLoading = false;
				})
				.catch(({message}) => {
					this.$message.error('保存项目申报情况失败,' + message);
					this.nextLoading = false;
				});
		},
		// 上传附件保存
		handleUploadedMaterialSave() {
			// 补充材料
			if (this.isMaterialMode) {
				let params = {
					save_type: 5,
					id: this.$route.query.id,
					file_list: this.assembleMaterialList(true)
				};

				this.$axios.post(APPLY_UPDATE, params)
					.then(() => {
						// 清除本地保存的草稿
						this.clearIntervalSave();
						this.showDialog({
							type: 'success',
							title: '提交成功',
							message: '您的申报已经提交成功，请密切留意最新的审核情况',
							errorTip: '',
							buttonText: '去看看',
							onButtonClick: () => {
								this.$router.push('/personal/record');
							}
						});
						this.submitLoading = false;
					})
					.catch(({message}) => {
						this.$message.error('保存上传材料失败,' + message);
						this.submitLoading = false;
					});
			} else {
				let params = {
					save_type: 4,
					id: this.draftId,
					file_list: this.assembleMaterialList()
				};

				if (this.isEdit && this.$route.query.apply_status == 4) {
					params.id = this.childrenID;
				}

				this.$axios.post(this.isCorrectMode ? CORRECT_UPDATE : APPLY_UPDATE, params)
					.then(() => {
						// 清除本地保存的草稿
						this.clearIntervalSave();
						this.showDialog({
							type: 'success',
							title: '提交成功',
							message: '您的申报已经提交成功，请密切留意最新的审核情况',
							errorTip: '',
							buttonText: '去看看',
							onButtonClick: () => {
								this.$router.push('/personal/record');
							}
						});
						this.submitLoading = false;
					})
					.catch(({message}) => {
						this.$message.error('保存上传材料失败,' + message);
						this.submitLoading = false;
					});
			}
		},
		// 查看模式点击上一页
		handleLookPre() {
			if (this.activeTabName === 'second') {
				this.activeTabName = 'first';
			} else if (this.activeTabName === 'third') {
				this.activeTabName = 'second';
			} else if (this.activeTabName === 'forth') {
				this.activeTabName = 'third';
			}
		},
		// 查看模式点击下一页
		handleLookNext() {
			if (this.activeTabName === 'first') {
				this.activeTabName = 'second';
			} else if (this.activeTabName === 'second') {
				this.activeTabName = 'third';
			} else if (this.activeTabName === 'third') {
				this.activeTabName = 'forth';
			}
		},
		// 上一页
		handlePre() {
			if (this.activeTabName === 'second') {
				this.activeTabName = 'first';
			} else if (this.activeTabName === 'third') {
				this.activeTabName = 'second';
			}
		},
		// 下一页
		handleNext() {
			if (this.activeTabName == 'first') {
				if (this.isMaterialMode) {
					this.activeTabName = 'second';
					return;
				}

				Promise.all([
					this.checkEnterpriseBasicInput(),
					this.checkEnterpriseContactInput(),
					this.checkEnterpriseEconomyInput()
				])
					.then(() => {
						// 保存企业信息
						this.nextLoading = true;
						this.handleEnterpriceInfoSave();
					})
					.catch(({code, message}) => {
						if (code == 22001) {
							this.$message.error(message);
							this.nextLoading = false;
						} else {
							this.$message.error('本页信息填写不完整，请检查');
							this.nextLoading = false;
						}
					});
			} else if (this.activeTabName == 'second') {
				if (this.isMaterialMode) {
					this.activeTabName = 'third';
					return;
				}
				this.checkDeclareInfoInput()
					.then(() => {
						this.nextLoading = true;
						this.handleDeclareSituationSave();
					})
					.catch(({code}) => {
						if (code == 13008) {
							console.log(code);
						} else {
							this.$message.error('本页信息填写不完整，请检查');
							this.nextLoading = false;
						}
					});
			} else if (this.activeTabName == 'third') {
				if (this.checked) {
					this.checkMaterialUpload()
						.then(() => {
							this.submitLoading = true;
							this.handleUploadedMaterialSave();
						})
						.catch(({code}) => {
							if (code == 13008) {
								console.log(code);
							} else {
								if (this.isMaterialMode) {
									this.$message.error('补充材料未上传，请检查');
								} else {
									this.$message.error('必传文件未上传，请检查');	this.$message.error('必传文件未上传，请检查');
								}

								this.submitLoading = false;
							}
						});
				} else {
					this.$message.error('请阅读并勾选诚信申报承诺');
					this.submitLoading = false;
				}
			}
		},
		// 点击选择
		handleSelect() {
			console.log('handleSelect');
			this.errorPrompt = true;
		},
		// 点击上传按钮
		handleUploadClick(material) {
			this.currentMaterial = material;
			this.currentUploadFiles = [];
			this.uploadDialogVisible = true;
		},
		// 点击上传确定确认按钮
		handleUploadConfirm() {
			let oldFiles = this.uploadedMaterialMap[this.currentMaterial.id] || [];
			let newFiles = [...oldFiles, ...this.currentUploadFiles];

			this.uploadedMaterialMap = {
				...this.uploadedMaterialMap,
				[this.currentMaterial.id]: newFiles
			};

			// 清空数据
			this.currentMaterial = null;
			this.currentUploadFiles = [];
			this.uploadDialogVisible = false;
		},
		// 点击重新上传
		handleReuploadClick() {
			this.currentUploadFiles = [];
		},
		// 文件上传限制
		beforeUpload(file) {
			console.log(file);
			let filename = file.name || '';
			let temp = filename.substring(filename.lastIndexOf('.') + 1) || '';
			let ext = temp.toLocaleLowerCase();
			let extArr = ['jpg', 'doc', 'png', 'bmp', 'jpeg', 'wps', 'docx', 'pdf', 'xls', 'xlsx'];
			let imageArr = ['jpg', 'png', 'bmp', 'jpeg'];

			if (this.currentMaterial.type == 2) {
				if (imageArr.indexOf(ext) < 0) {
					if (this.errorPrompt) {
						this.$message({
							message: '上传发票只能是JPG，JPEG, PNG, BMP格式的图片',
							type: 'warning'
						});
						this.errorPrompt = false;
					}
					return false;
				}
			} else {
				if (extArr.indexOf(ext) < 0) {
					if (this.errorPrompt) {
						this.$message({
							message: '上传文件只能是JPG，JPEG, PNG, BMP, DOC，WPS，DOCX，PDF，XLS，XLSX格式的附件',
							type: 'warning'
						});
						this.errorPrompt = false;
					}
					return false;
				}
			}

			const isLt2M = file.size / 1024 / 1024 < 100;
			const isLtSmall = file.size / 1024 / 1024 < 2;

			if (this.currentMaterial.type == 2) {
				if (!isLtSmall) {
					if (this.errorPrompt) {
						this.$message({
							message: '发票上传大小不能超过2MB!',
							type: 'warning'
						});
						this.errorPrompt = false;
					}
					return false;
				}
			} else {
				if (!isLt2M) {
					if (this.errorPrompt) {
						this.$message({
							message: '上传文件大小不能超过 100MB!',
							type: 'warning'
						});
						this.errorPrompt = false;
					}
					return false;
				}
			}

			return true;
		},
		// 自定义上传请求
		customUpload({file, data, filename, onProgress}) {
			let params = new FormData();

			params.append(filename, file);
			if (data) {
				for (let key in data) {
					params.append(key, data[key]);
				}
			}
			let selfCanceler = null;
			let request = this.$axios.post(UPLOAD_FILE, params, {
				cancelToken: new axios.CancelToken(canceler => {
					selfCanceler = canceler;
				}),
				onUploadProgress: ({lengthComputable, loaded, total}) => {
					if (lengthComputable) {
						onProgress({percent: Math.floor(loaded / total * 100)});
					}
				},
				headers: {'Content-Type': 'multipart/form-data'}
			});

			request.abort = () => {
				console.log('request abort');
				selfCanceler && selfCanceler();
			};

			return request;
		},
		// 处理文件上传中
		handleUploadChange(file) {
			let theFile = {
				uid: file.uid,
				status: file.status,
				file_name: file.name,
				file_type: this.currentMaterial.type,
				project_materials_id: this.currentMaterial.id,
			};

			if (file.response) {
				theFile.file_url = file.response.url;
				theFile.created_at = file.response.created_at;
			}

			let files = this.currentUploadFiles ? [...this.currentUploadFiles] : [];
			let index = files.findIndex(item => item.uid === file.uid);

			if (index >= 0) {
				files[index] = {
					...files[index],
					...theFile,
				};
			} else {
				files.push(theFile);
			}

			this.currentUploadFiles = files;
		},
		// 上传失败的回调
		handleUploadError(err, file) {
			let errorMsg = err && err.message || '未知原因';

			let files = this.currentUploadFiles ? [...this.currentUploadFiles] : [];
			let index = files.findIndex(item => item.uid === file.uid);

			if (index >= 0) {
				files[index] = {
					...files[index],
					errorMsg
				};

				this.currentUploadFiles = files;
			}
		},
		// 上传中的回调
		handleUploadProgress(event, file) {
			// 进度减一，为了避免上传进度为100%卡住的问题
			let percent = event.percent > 1 ? event.percent - 1 : event.percent;

			let files = this.currentUploadFiles ? [...this.currentUploadFiles] : [];
			let index = files.findIndex(item => item.uid === file.uid);

			if (index >= 0) {
				files[index] = {
					...files[index],
					percent: percent
				};

				this.currentUploadFiles = files;
			}
		},
		// 删除某一个上传文件
		handleUploadRemove(file) {
			let files = this.currentUploadFiles ? [...this.currentUploadFiles] : [];
			let removedFiles = files && files.filter(item => item.uid !== file.uid);

			this.currentUploadFiles = removedFiles;
		},
		// 删除某一条上传文件
		handleFileRemove(materialId, fileUid, tgrgetIndex) {
			let files = this.uploadedMaterialMap[materialId] || [];

			let removedFiles = files && files.filter((item, index) => index !== tgrgetIndex);

			// 保存为新对象以触发UI刷新
			this.uploadedMaterialMap = {
				...this.uploadedMaterialMap,
				[materialId]: removedFiles
			};
		},
		// 删除最后一个文件
		handleLastFileRemove(id) {
			this.uploadedMaterialMap[id] = [];
		},
		// 组装经济表格数据
		assembleEconomyList() {
			let economyList = [];

			this.enterpriseEconomyTableForm.forEach(item => {
				for (let i = 0; i < item.contentOfYears.length; i++) {
					let content = item.contentOfYears[i];

					economyList.push({
						type: item.type,
						content: content,
						year: i === 0 ? this.getYear.preTowYear : (i === 1 ? this.getYear.preYear : this.getYear.currentYear)
					});
				}
			});
			return economyList;
		},
		// 组装材料列表数据
		assembleMaterialList(isAddition = false) {
			let materialList = [];

			// if (this.isEdit && this.$route.query.apply_status == 4) {
			// 	materialList = this.uploadedMaterialMap;
			// 	return materialList;
			// }
			for (let key in this.uploadedMaterialMap) {
				let files = this.uploadedMaterialMap[key];

				if (files && files.length > 0) {
					// map是为了去掉后端不需要的数据
					let filterdFiles = files && files.filter(file => file.status === 'success')
						.filter(file => !isAddition || file.file_type === 0)
						.map(file => ({
							file_name: file.file_name,
							file_url: file.file_url,
							file_type: file.file_type,
							project_materials_id: file.project_materials_id,
							id: file.id || ''
						}));

					materialList.push(...filterdFiles);
				}
			}

			return materialList;
		},
		// 显示申报结果的弹窗
		showDialog(dialog) {
			this.dialog = {
				...dialog,
				visible: true
			};
		},
		// 表格样式控制
		tableHeaderColor({rowIndex}) {
			if (rowIndex === 0) {
				return 'background-color: #005192;color: #fff;font-weight: 500;';
			}
		},
		tableCellColor({columnIndex}) {
			if (columnIndex === 1) {
				return 'font-weight: 500;color: #3B3B3B;';
			}
			if (columnIndex == 4) {
				return 'color: #005192;';
			}
		},
		handleCellColor({columnIndex}) {
			if (columnIndex === 0) {
				return 'font-weight:bold; background-color: #F9FBFC;';
			}
		},
		handleHeaderCellColor({rowIndex, columnIndex}) {
			if (columnIndex === 0) {
				return 'background-color: #F9FBFC;';
			}
			if (rowIndex == 0) {
				return 'font-weight:bold;';
			}
		},
	},
	created() {
		// fix 数据初始化后设置表单默认值无效
		this.$nextTick(() => {
			// 初始化企业相关信息的表单数据
			this.initEnterpriseForms();
			// 初始化项目申报情况的表单数据
			this.initDeclareSituationForm();
			// 初始化材料上传的表单数据
			this.initMaterialUploadForm();
		});
	},
	mounted() {
		if (this.isEdit) {
			this.noAcceptedID = this.$route.query.id;
		}
		this.startIntervalSave();

		if (this.isMaterialMode) {
			this.activeTabName = 'third';
		}
	},
	beforeDestroy() {
		this.stopIntervalSave();
	},
};
</script>
<style lang="less" >
@import "~assets/css/common_avairail.less";
.declare-online-container {
  width: 100%;
  .content {
    background: @backGroundColor;
    width: 100%;
    padding: 0 50px 50px 50px;

    h1 {
      font-size: 30px;
      line-height: 40px;
      color: @boldTextColor;
      padding: 38px 0;
      text-align: center;
    }

    .explain {
      display: flex;
      flex-direction: row;
      align-items: center;
      padding: 18px 32px;
      background: @applyItemBgColor;
      border: 1px solid #bcd5e9;

      img {
        width: 52px;
        height: 52px;
        margin-right: 34px;
      }

      .right {
        h5 {
          font-size: 14px;
          line-height: 22px;
          color: @boldTextColor;
          margin-bottom: 10px;
        }

        p {
          font-size: 14px;
          font-weight: 400;
          line-height: 22px;
          color: @textColor;
        }
      }
    }
  }
  .link-type {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;

    .left {
      display: flex;
      align-items: center;
      width: 45%;
    }

    .right {
      display: flex;
      align-items: center;
      width: 45%;
    }

    span {
      font-size: 16px;
      font-weight: 400;
      line-height: 19px;
      color: @textColor;
      width: 85px;
    }

    p {
      padding: 9px 12px;
      border: 1px solid rgba(229, 232, 236, 1);
      font-size: 14px;
      font-weight: 400;
      line-height: 17px;
      color: @boldTextColor;
      flex: 1;
    }
  }
  .el-tabs--border-card {
    border: 1px solid #dcdfe6;
    border-bottom: none;
    box-shadow: none;
  }
  .el-tabs--border-card > .el-tabs__header {
    background-color: @applyItemBgColor;
    border-bottom: 1px solid #dcdfe6;
  }
  .el-form-item {
    margin: 0;
  }
  .form-first {
    padding: 10px 10px;

    .form-title {
      display: flex;
      align-items: center;
      height: 28px;

      .vertical {
        width: 6px;
        height: 28px;
        background: @primaryColor;
        margin-right: 10px;
      }

      h3 {
        font-size: 17px;
        font-weight: 400;
        line-height: 22px;
        color: @boldTextColor;
        margin-right: 10px;
      }

      .error-tip {
        font-size: 13px;
        font-weight: 400;
        line-height: 22px;
        color: @tipsColor;
        display: flex;
        align-items: center;

        img {
          width: 14px;
          height: 14px;
          display: inline-block;
          margin-right: 5px;
        }
      }
    }

    .form-table {
      width: 100%;
      border: 1px solid rgba(220, 223, 230, 1);
      border-collapse: collapse;
      margin-top: 20px;
      margin-bottom: 25px;


      tr td {
        min-height: 40px;
        line-height: 40px;
        border: 1px solid rgba(220, 223, 230, 1);
        text-align: right;
        padding: 0 20px;
      }
      tr td.odd {
        width: 176px;
        background: @applyItemBgColor;
        color: @textColor;
        font-size:14px;
        font-family:Microsoft YaHei;
        font-weight:bold;
      }

      tr td.odd2 {
        width: 120px;
        text-align: center;
      }

      tr td.odd3 {
        width: 203px;
      }

      tr td.year {
        text-align: center;
      }

      tr td.even {
        input {
          width: 100%;
          // height: 30px;
          border: none;
          outline: 0;
          font-size: 14px;
          font-weight: 400;
          line-height: 22px;
          color: @boldTextColor;
        }
      }

      tr td.year {
        input {
          width: 100%;
          height: 30px;
          border: none;
          outline: 0;
          font-size: 14px;
          font-weight: 400;
          line-height: 22px;
          color: @boldTextColor;
          text-align: center;
        }
      }
      .el-input__inner {
        padding: 0;
      }
      .regist_time {
        .el-input__inner {
        padding-left: 30px;
      }
      }
    }
    .economy-table {
      margin-top: 20px;
      font-size:14px;
      font-family:Microsoft YaHei;
      th {
        font-weight:400;
      }
      td {
        padding: 0;
      }
      .cell {
        width: 100%;
        padding: 0;
      }
      .el-input__inner {
        border: 0;
        text-align: center;
        padding: 0;
      }
      .el-form-item {
        height: 100%;
        margin-bottom: 0;
        padding: 2px 0;
      }
    }
    .textarea {
      vertical-align: middle;
      .el-textarea__inner {
        padding-left: 0;
        padding-right: 0;
        border: 0;
        resize:none;
        color: #3B3B3B;
        font-family: inherit;
      }
    }
  }
  .form-seconed {
    padding: 10px 15px;
    .el-form-item {
      margin: 0;
    }
    .form-title {
      display: flex;
      align-items: center;
      height: 28px;
      margin-bottom: 20px;
      .vertical {
        width: 6px;
        height: 28px;
        background: @primaryColor;
        margin-right: 10px;
      }

      h3 {
        font-size: 17px;
        font-weight: 400;
        line-height: 22px;
        color: @boldTextColor;
        margin-right: 10px;
      }
    }
    .error-tip {
      font-size: 13px;
      font-weight: 400;
      line-height: 22px;
      color: @tipsColor;
      display: flex;
      align-items: center;

      img {
        width: 14px;
        height: 14px;
        display: inline-block;
        margin-right: 5px;
      }
    }
    .content-box {
      margin-bottom: 20px;
      .content-title {
        width: 100%;
        height: 39px;
        background: @applyItemBgColor;
        line-height: 39px;
        padding-left: 20px;
        font-size: 14px;
        font-family: Microsoft YaHei;
        font-weight: bold;
        color: #818181;
        border: 1px solid @contentBoxColor;
        border-bottom: 0;
      }
      .el-textarea__inner {
        border-radius: 0;
        height: 98px;
      }
      .form-row {
        border: 1px solid @contentBoxColor;
      }
      .label {
        width: 100%;
        padding: 0 10px;
        text-align: center;
        line-height: 40px;
        background: @applyItemBgColor;
        font-size: 14px;
        font-family: Microsoft YaHei;
        font-weight: bold;
      }
      .form-input {
        width: 100%;
        .el-input__inner {
          border-radius: 0;
          outline: none;
        }
      }
      .form-row-wither {
        margin-top: 20px;
        height: 80px;
         .form-row {
          border: 1px solid @contentBoxColor;
        }
        .label {
          width: 100%;
          text-align: center;
          background: @applyItemBgColor;
          font-size: 14px;
          font-family: Microsoft YaHei;
          font-weight: bold;
        }
        .form-input {
          width: 100%;
          height: 80px;
          .el-input__inner {
            border-radius: 0;
            outline: none;
            height: 100%;
          }
        }
      }
    }
  }
  .form-third {
    padding: 0 20px;
    .form-title {
      display: flex;
      align-items: center;
      height: 28px;
      margin-bottom: 20px;
      .vertical {
        width: 6px;
        height: 28px;
        background: @primaryColor;
        margin-right: 10px;
      }

      h3 {
        font-size: 17px;
        font-weight: 400;
        line-height: 22px;
        color: @boldTextColor;
        margin-right: 10px;
      }
    }
    .error-tip {
      font-size: 13px;
      font-weight: 400;
      line-height: 22px;
      color: @tipsColor;
      display: flex;
      align-items: center;

      img {
        width: 14px;
        height: 14px;
        display: inline-block;
        margin-right: 5px;
      }
    }
    .form-table {
      width: 100%;
      border: 1px solid  #DCDFE6;
      .table-title {
        background:  @applyItemBgColor;
        padding: 5px 10px;
        color: @textColor;
      }
      .tips {
        h3 {
          padding-bottom: 5px;
        }
        padding: 20px 33px;
        font-size:14px;
        font-family:Microsoft YaHei;
        font-weight:400;
        .red-tip {
          color:  @tipsColor;
        }
      }
      .material-table-box {
        padding: 20px 33px;
        .material-table {
          text-align: center;
          .el-table__header thead {
            background: @primaryColor;
          }
        }
        .upload-btn {
          span {
            text-decoration: underline;
          }
        }
        .el-table__row {
          td:first-child {
            border-left: 1px solid #EBEEF5;
          }
        }
      }
    }

    .explain-container {
      width: 100%;
      border: 1px solid #DCDFE6;
      margin: 20px 0;
      .explain-title {
        padding: 5px 20px 5px 10px;
        background:  @applyItemBgColor;
        color: @textColor;
        display: flex;
        justify-content: space-between;
      }
      .explain-cotent {
        padding: 0px 33px 20px 33px;

        h3 {
          padding: 5px 0;
        }
      }
      .content-item {
        margin-top: 20px;
        .content-tip {
          color: @textColor;

        }
      }
    }

    .upload-files {
      .info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: @primaryColor;
        text-decoration: underline;
      }
      .upload-time {
        text-align: left;
        font-size: 12px;
      }
    }
    .el-popover {
      color: red;
    }
  }
 .form-forth {
   padding: 0 20px;
      .form-title {
      display: flex;
      align-items: center;
      height: 28px;

      .vertical {
        width: 6px;
        height: 28px;
        background: @primaryColor;
        margin-right: 10px;
      }

      h3 {
        font-size: 17px;
        font-weight: 400;
        line-height: 22px;
        color: @boldTextColor;
        margin-right: 10px;
      }

      .error-tip {
        font-size: 13px;
        font-weight: 400;
        line-height: 22px;
        color: @tipsColor;
        display: flex;
        align-items: center;

        img {
          width: 14px;
          height: 14px;
          display: inline-block;
          margin-right: 5px;
        }
      }
    }
    .approval-content {
      padding: 0;
      margin-top: 20px;
      .content-item {
        margin-bottom: 20px;
        table {
          width: 100%;
           border-collapse: collapse;
           td {
              border: 1px solid @defaultBorderColor;
           }
        }
        thead {
          .col-head {
            font-size:14px;
            font-family:Microsoft YaHei;
            font-weight:bold;
            color: @textColor;
            padding: 15px 0;
            background: #F9FBFC;
            padding-left: 20px;
          }
        }
        tbody {
          .col-1 {
            width: 15%;
            font-size:14px;
            font-family:Microsoft YaHei;
            font-weight:bold;
            color: @textColor;
             background: #F9FBFC;
             text-align: center;
          }
          .col-2 {
            padding: 15px;
          }
        }
      }
    }
    .role {
      color: @poliyItemColor;
    }
  }

  .link-btn {
    padding: 20px 30px;
    background: @applyItemBgColor;
    border: 1px solid #dcdfe6;
    border-top: none;
    display: flex;
    justify-content: space-between;
    .link-btn-right {
      width: 500px;
      display: flex;
      justify-content: flex-start;
    }
    .el-button {
      border-radius: 0;
      margin-right: 20px;
    }
  }
  .image-uploader {
    .el-upload--picture-card {
      width: 80px;
      height: 80px;
      border: 1px dashed #d9d9d9;
      border-radius: 6px;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }
    .el-upload:hover {
      border-color: @primaryColor;
    }
  }
  .btn-container {
    .el-form-item__content {
      .el-button--default {
        width: 45%;
        height: 55px;
        border-radius: 0;
        border: 1px solid @primaryColor;
        font-size: 23px;
        color: @primaryColor;
      }
      .el-button--primary {
        width: 45%;
        height: 55px;
        border-radius: 0;
        background: @primaryColor;
        font-size: 23px;
        font-weight: Bold;
        font-family: Microsoft YaHei;
      }
    }
  }
  .license-tip {
    line-height: 25px;
  }
  .upload-container {
    .el-upload {
      width: 100%;
    }
    .el-upload-dragger {
      padding: 10px 0;
      width: 100%;
      height: auto;
      .image-btns {
        margin-top: 80px;
      }
    }
    .el-upload-dragger {
      border: 0;
    }
    .upload-icon {
        width: 180px;
        height: 100px;
        margin: auto;
      }
      .el-upload__btn {
        margin-top: 20px;
        .el-button--primary {
          border-radius: 0;
        }
      }
      .el-upload__tip {
        margin-top: 20px;
        text-align: center;
        font-size:14px;
        font-family:Microsoft YaHei;
        font-weight:400
      }
  }

  .card {
    padding: 20px;
    .title {
      color: @textColor;
      padding: 5px 0;
    }
    .item {
      display: flex;
      justify-content: space-between;
      color: red;
    }
    .primaryColor {
       color: @primaryColor;
    }
  }
  .industy-selection {
    width: 100%;
  }
  .date-picker{
    width: 100%;
  }
  .upload-file-box {
    padding: 0 8px;
    .box-title {
      font-size: 14px;
      color: @textColor;
      padding: 5px 0;
    }
    .upload-file-item {
      display: flex;
      align-items: center;
      margin: 10px 0;
      padding: 16px;
      border-radius: 5px;
      border: 1px solid #F9FBFC;
      background-color: #F9FBFC;
      .file-thumbnail {
        width: 60px;
        height: 40px;
      }
      .file-content-wrap {
        flex: 1;
        margin-left: 16px;
        .file-name {
          font-size: 16px;
          color: #3B3B3B;
          font-weight: bold;
        }
        .file-upload-progress {
          margin-top: 4px;
        }
        .upload-fail-tip {
          color:#F56C6C;
        }
      }
      .file-remove-btn {
        width: 20px;
        height: 20px;
        margin: 0 8px;
        cursor: pointer;
      }
    }
    .box-action-buttons {
      padding: 16px 0;
      text-align: center;
      .action-button {
        min-width: 130px;
      }
    }
  }
  .step-box {
    width: 100%;
    padding: 30px 0 30px 80px;
    .step {
      margin: auto;
      // margin-left: 30px;
    }
  }
}
</style>

